<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessarCsvsJob;
use App\Models\Cliente;
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

        ProcessarCsvsJob::dispatch($caminhos, $request->cliente_id);

        return back()->with('success', 'Arquivos enviados! O processamento será feito em background e pode levar alguns minutos.');
    }
}