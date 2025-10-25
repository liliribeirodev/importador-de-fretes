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
        Schema::create('tabelas_frete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('cep_origem', 20);
            $table->string('cep_destino', 20);
            $table->decimal('peso_inicial', 10, 2);
            $table->decimal('peso_final', 10, 2);
            $table->decimal('valor', 10, 2);
            $table->unsignedBigInteger('filial_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabelas_frete');
    }
};
