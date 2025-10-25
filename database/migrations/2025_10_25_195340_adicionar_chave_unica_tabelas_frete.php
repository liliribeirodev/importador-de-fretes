<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tabelas_frete', function (Blueprint $table) {
            $table->unique(['cliente_id', 'cep_origem', 'cep_destino', 'peso_inicial', 'peso_final'], 'idx_frete_unico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabelas_frete', function (Blueprint $table) {
            $table->dropUnique('idx_frete_unico');
        });
    }
};