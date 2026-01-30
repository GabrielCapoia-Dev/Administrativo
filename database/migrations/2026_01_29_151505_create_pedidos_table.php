<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_protocolo')->unique();
            $table->text('descricao');
            
            // Relacionamentos
            $table->foreignId('tipo_manutencao_id')->constrained('tipo_manutencao');
            $table->foreignId('tipo_status_id')->constrained('tipo_status');
            $table->foreignId('escola_id')->constrained('escolas');
            $table->foreignId('solicitante_id')->constrained('users'); // Quem criou o pedido
            $table->foreignId('responsavel_educacao_id')->nullable()->constrained('users');
            $table->foreignId('responsavel_obras_id')->nullable()->constrained('users');
            
            // Datas
            $table->date('data_prevista')->nullable();
            $table->date('data_entrega')->nullable();
            $table->date('data_cancelamento')->nullable();
            $table->integer('quantidade_dias_prorrogado')->default(0);
            
            $table->timestamps();

            // Campos de histórico
            $table->boolean('ativo')->default(true);
            $table->foreignId('registro_anterior_id')
                ->nullable()
                ->constrained('pedidos')
                ->nullOnDelete();

            $table->index(['numero_protocolo', 'ativo']);
            $table->index(['tipo_status_id', 'ativo']);
            $table->index(['escola_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};