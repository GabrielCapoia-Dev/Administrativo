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
        Schema::create('escolas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            // Campos de histórico
            $table->boolean('ativo')->default(true);
            $table->foreignId('registro_anterior_id')
                ->nullable()
                ->constrained('escolas')
                ->nullOnDelete();

            // Índice para buscar registros ativos por nome
            $table->index(['nome', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escolas');
    }
};