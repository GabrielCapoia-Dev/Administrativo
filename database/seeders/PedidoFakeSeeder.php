<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Escola;
use App\Models\Pedido;
use App\Models\TipoManutencao;
use App\Models\TipoStatus;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PedidoFakeSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Criar escolas (3 a 5) =====

        $qtdEscolas = rand(3, 5);
        $escolas = collect();

        for ($i = 1; $i <= $qtdEscolas; $i++) {

            $escolas->push(
                Escola::create([
                    'nome' => "Escola Teste {$i}",
                    'telefone' => '(44) 99999-000' . $i,
                    'email' => "escola{$i}@teste.com",
                    'ativo' => true,
                ])
            );
        }

        // ===== Criar usuários (solicitantes) por escola =====

        $solicitantesPorEscola = [];

        foreach ($escolas as $escola) {

            $qtdUsuarios = rand(1, 3);

            for ($i = 1; $i <= $qtdUsuarios; $i++) {

                $user = User::create([
                    'name' => "Funcionário {$i} - {$escola->nome}",
                    'email' => Str::slug($escola->nome) . "_{$i}@teste.com",
                    'password' => Hash::make('Senha@123'),
                    'email_verified_at' => now(),
                    'email_approved' => true,
                    'id_escola' => $escola->id,
                ]);

                $solicitantesPorEscola[$escola->id][] = $user;
            }
        }

        // ===== Buscar tipos =====

        $tiposManutencao = TipoManutencao::all();
        $tiposStatus = TipoStatus::all();

        // ===== Criar pedidos =====

        foreach ($escolas as $escola) {

            $solicitantes = $solicitantesPorEscola[$escola->id];

            foreach ($tiposManutencao as $manutencao) {

                foreach ($tiposStatus as $status) {

                    $qtdPedidos = rand(1, 4);

                    for ($i = 1; $i <= $qtdPedidos; $i++) {

                        $solicitante = collect($solicitantes)->random();

                        Pedido::create([
                            'escola_id' => $escola->id,
                            'solicitante_id' => $solicitante->id,

                            'tipo_manutencao_id' => $manutencao->id,
                            'tipo_status_id' => $status->id,

                            'descricao' => "Pedido de {$manutencao->nome} - {$status->nome}",

                            'data_prevista' => Carbon::now()->addDays(rand(3, 30)),

                            'data_entrega' => in_array(strtolower($status->nome), ['finalizado','concluído'])
                                ? Carbon::now()->addDays(rand(1, 40))
                                : null,

                            'ativo' => true,
                        ]);
                    }
                }
            }
        }

        $this->command->info('Banco populado com escolas, usuários e pedidos fake!');
    }
}
