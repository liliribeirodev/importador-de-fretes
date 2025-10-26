<?php

namespace App\Jobs;

use App\Models\TabelaFrete;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessarCsvsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    protected array $arquivos;
    protected int $clienteId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $arquivos, int $clienteId)
    {
        $this->arquivos = $arquivos;
        $this->clienteId = $clienteId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->arquivos as $caminhoArquivo)
        {
            $caminhoCompleto = Storage::disk('local')->path($caminhoArquivo);

            if (!file_exists($caminhoCompleto)) {
                continue;
            }

            $arquivo = fopen($caminhoCompleto, 'r');

            if ($arquivo === false) {
                continue;
            }

            $cabecalho = null;
            $lote = [];

            while (($linha = fgetcsv($arquivo, 0, ',')) !== false)
            {
                if (!$linha || !is_array($linha) || count($linha) === 0) {
                    continue;
                }

                if (!$cabecalho) {
                    $cabecalho = array_map('trim', $linha);
                    if (count($cabecalho) < 5) { 
                        \Log::error('Cabeçalho inválido: menos colunas que o esperado', ['linha' => $linha]);
                        return;
                    }
                    continue;
                }

                if (count($linha) !== count($cabecalho)) {
                    \Log::warning('Linha ignorada: número de colunas diferente do cabeçalho', [
                        'linha' => $linha,
                        'cabecalho' => $cabecalho,
                    ]);
                    continue;
                }

                $dados = array_combine($cabecalho, $linha);

                $cepOrigem = preg_replace('/\D/', '', $dados['from_postcode'] ?? '');
                $cepDestino = preg_replace('/\D/', '', $dados['to_postcode'] ?? '');
                $pesoInicial = (float) str_replace(',', '.', $dados['from_weight'] ?? 0);
                $pesoFinal = (float) str_replace(',', '.', $dados['to_weight'] ?? 0);
                $valor = (float) str_replace(',', '.', $dados['cost'] ?? 0);

                $lote[] = [
                    'cliente_id' => $this->clienteId,
                    'cep_origem' => $cepOrigem,
                    'cep_destino' => $cepDestino,
                    'peso_inicial' => $pesoInicial,
                    'peso_final' => $pesoFinal,
                    'valor' => $valor,
                    'filial_id' => $dados['branch_id'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($lote) >= 1000) {
                    TabelaFrete::upsert(
                        $lote,
                        ['cliente_id', 'cep_origem', 'cep_destino', 'peso_inicial', 'peso_final'],
                        ['valor', 'filial_id', 'updated_at']
                    );
                    $lote = [];
                }
            }

            if (count($lote) > 0) {
                TabelaFrete::upsert(
                    $lote,
                    ['cliente_id', 'cep_origem', 'cep_destino', 'peso_inicial', 'peso_final'],
                    ['valor', 'filial_id', 'updated_at']
                );
            }

            fclose($arquivo);

            Storage::disk('local')->delete($caminhoArquivo);
        }
    }
}