<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessarCsvsJob;
use App\Models\Cliente;
use App\Models\Importacao;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function mostraFormulario()
    {
        $clientes = Cliente::all();
        return view('upload', compact('clientes'));
    }

    public function uploadCsvs(Request $request)
    {
        if(!$request->hasFile('arquivos')) {
            \Log::error('Nenhum arquivo recebido!');
        } else {
            foreach ($request->file('arquivos') as $file) {
                \Log::info('Arquivo recebido: '.$file->getClientOriginalName().' - '.$file->getSize().' bytes');
            }
        }

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'arquivos.*' => 'required|file|mimes:csv|max:102400',
        ]);

        $caminhos = [];

        foreach ($request->file('arquivos') as $arquivo) {
            $nomeArquivo = $arquivo->getClientOriginalName();
            $caminho = Storage::disk('local')->putFileAs('uploads', $arquivo, $nomeArquivo);
            $caminhos[] = $caminho;
        }

        $importacao = Importacao::create([
            'cliente_id' => $request->cliente_id,
            'status' => 'processing',
            'arquivos' => $caminhos,
        ]);

        ProcessarCsvsJob::dispatch($caminhos, $request->cliente_id, $importacao->id);

        return back()
            ->with('importacao_id', $importacao->id)
            ->with('success', 'Arquivos enviados! O sistema está processando...');
    }

    public function statusImportacao($id)
    {
        $importacao = Importacao::findOrFail($id);
        return response()->json(['status' => $importacao->status]);
    }

    public function completeImportacao(Request $request, $id)
    {
        if ((string) session('importacao_id') !== (string) $id) {
            return response()->json(['ok' => false, 'message' => 'ID de importação incompatível'], 403);
        }

        session()->forget('importacao_id');
        session()->flash('success', 'Importação concluída com sucesso!');

        return response()->json(['ok' => true]);
    }
}