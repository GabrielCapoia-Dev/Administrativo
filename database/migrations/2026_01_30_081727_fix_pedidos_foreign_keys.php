<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Remove as constraints existentes
            $table->dropForeign(['solicitante_id']);
            $table->dropForeign(['responsavel_educacao_id']);
            $table->dropForeign(['responsavel_obras_id']);

            // Recria com nullOnDelete
            $table->foreign('solicitante_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('responsavel_educacao_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('responsavel_obras_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['solicitante_id']);
            $table->dropForeign(['responsavel_educacao_id']);
            $table->dropForeign(['responsavel_obras_id']);

            $table->foreign('solicitante_id')->references('id')->on('users');
            $table->foreign('responsavel_educacao_id')->references('id')->on('users');
            $table->foreign('responsavel_obras_id')->references('id')->on('users');
        });
    }
};