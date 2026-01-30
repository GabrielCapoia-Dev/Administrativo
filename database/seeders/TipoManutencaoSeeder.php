<?php

namespace Database\Seeders;

use App\Models\TipoManutencao;
use Illuminate\Database\Seeder;

class TipoManutencaoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nome' => 'Elétrica', 'descricao' => 'Problemas elétricos, fiação, tomadas, iluminação'],
            ['nome' => 'Hidráulica', 'descricao' => 'Encanamento, vazamentos, torneiras, sanitários'],
            ['nome' => 'Estrutural', 'descricao' => 'Paredes, telhado, piso, rachaduras'],
            ['nome' => 'Pintura', 'descricao' => 'Pintura interna e externa'],
            ['nome' => 'Marcenaria', 'descricao' => 'Portas, janelas, móveis, armários'],
            ['nome' => 'Serralheria', 'descricao' => 'Grades, portões, estruturas metálicas'],
            ['nome' => 'Ar Condicionado', 'descricao' => 'Instalação, manutenção e reparo de climatização'],
            ['nome' => 'Jardinagem', 'descricao' => 'Poda, limpeza de área verde, plantio'],
            ['nome' => 'Outros', 'descricao' => 'Outros tipos de manutenção não listados'],
        ];

        foreach ($tipos as $tipo) {
            TipoManutencao::firstOrCreate(
                ['nome' => $tipo['nome']],
                $tipo
            );
        }
    }
}