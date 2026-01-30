<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('tipo_status_id')->constrained('tipo_status');
            $table->foreignId('user_id')->constrained('users'); // Quem fez a alteração
            $table->text('observacao')->nullable();
            $table->boolean('notificacao_enviada')->default(false);
            $table->timestamps();

            $table->index(['pedido_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_historicos');
    }
};