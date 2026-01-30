<?php

namespace Database\Seeders;

use App\Models\TipoStatus;
use Illuminate\Database\Seeder;

class TipoStatusSeeder extends Seeder
{
    public function run(): void
    {
        $status = [
            [
                'nome' => 'Aberto',
                'cor' => '#3B82F6', // blue
                'ordem' => 1,
                'finaliza_pedido' => false,
                'cancela_pedido' => false,
            ],
            [
                'nome' => 'Em Análise',
                'cor' => '#F59E0B', // amber
                'ordem' => 2,
                'finaliza_pedido' => false,
                'cancela_pedido' => false,
            ],
            [
                'nome' => 'Aguardando Obras',
                'cor' => '#8B5CF6', // violet
                'ordem' => 3,
                'finaliza_pedido' => false,
                'cancela_pedido' => false,
            ],
            [
                'nome' => 'Em Execução',
                'cor' => '#06B6D4', // cyan
                'ordem' => 4,
                'finaliza_pedido' => false,
                'cancela_pedido' => false,
            ],
            [
                'nome' => 'Concluído',
                'cor' => '#10B981', // emerald
                'ordem' => 5,
                'finaliza_pedido' => true,
                'cancela_pedido' => false,
            ],
            [
                'nome' => 'Cancelado',
                'cor' => '#EF4444', // red
                'ordem' => 6,
                'finaliza_pedido' => false,
                'cancela_pedido' => true,
            ],
        ];

        foreach ($status as $item) {
            TipoStatus::firstOrCreate(
                ['nome' => $item['nome']],
                $item
            );
        }
    }
}