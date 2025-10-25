<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TabelaFrete extends Model
{
    use HasFactory;

    protected $table = 'tabelas_frete';

    protected $fillable = [
        'cliente_id',
        'cep_origem',
        'cep_destino',
        'peso_inicial',
        'peso_final',
        'valor',
        'filial_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
