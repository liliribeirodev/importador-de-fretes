<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Importacao extends Model
{
    use HasFactory;

    protected $table = 'importacoes';

    protected $fillable = [
        'cliente_id',
        'status',
        'arquivos',
    ];

    protected $casts = [
        'arquivos' => 'array',
    ];
}
