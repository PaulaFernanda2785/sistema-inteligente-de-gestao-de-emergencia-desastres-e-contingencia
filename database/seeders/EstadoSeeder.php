<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nome' => 'Acre', 'sigla' => 'AC', 'codigo_ibge' => '12', 'regiao' => 'NORTE', 'ativo' => true],
            ['nome' => 'Alagoas', 'sigla' => 'AL', 'codigo_ibge' => '27', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Amapa', 'sigla' => 'AP', 'codigo_ibge' => '16', 'regiao' => 'NORTE', 'ativo' => true],
            ['nome' => 'Amazonas', 'sigla' => 'AM', 'codigo_ibge' => '13', 'regiao' => 'NORTE', 'ativo' => true],
            ['nome' => 'Bahia', 'sigla' => 'BA', 'codigo_ibge' => '29', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Ceara', 'sigla' => 'CE', 'codigo_ibge' => '23', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Distrito Federal', 'sigla' => 'DF', 'codigo_ibge' => '53', 'regiao' => 'CENTRO-OESTE', 'ativo' => true],
            ['nome' => 'Espirito Santo', 'sigla' => 'ES', 'codigo_ibge' => '32', 'regiao' => 'SUDESTE', 'ativo' => true],
            ['nome' => 'Goias', 'sigla' => 'GO', 'codigo_ibge' => '52', 'regiao' => 'CENTRO-OESTE', 'ativo' => true],
            ['nome' => 'Maranhao', 'sigla' => 'MA', 'codigo_ibge' => '21', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Mato Grosso', 'sigla' => 'MT', 'codigo_ibge' => '51', 'regiao' => 'CENTRO-OESTE', 'ativo' => true],
            ['nome' => 'Mato Grosso do Sul', 'sigla' => 'MS', 'codigo_ibge' => '50', 'regiao' => 'CENTRO-OESTE', 'ativo' => true],
            ['nome' => 'Minas Gerais', 'sigla' => 'MG', 'codigo_ibge' => '31', 'regiao' => 'SUDESTE', 'ativo' => true],
            ['nome' => 'Para', 'sigla' => 'PA', 'codigo_ibge' => '15', 'regiao' => 'NORTE', 'ativo' => true],
            ['nome' => 'Paraiba', 'sigla' => 'PB', 'codigo_ibge' => '25', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Parana', 'sigla' => 'PR', 'codigo_ibge' => '41', 'regiao' => 'SUL', 'ativo' => true],
            ['nome' => 'Pernambuco', 'sigla' => 'PE', 'codigo_ibge' => '26', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Piaui', 'sigla' => 'PI', 'codigo_ibge' => '22', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Rio de Janeiro', 'sigla' => 'RJ', 'codigo_ibge' => '33', 'regiao' => 'SUDESTE', 'ativo' => true],
            ['nome' => 'Rio Grande do Norte', 'sigla' => 'RN', 'codigo_ibge' => '24', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Rio Grande do Sul', 'sigla' => 'RS', 'codigo_ibge' => '43', 'regiao' => 'SUL', 'ativo' => true],
            ['nome' => 'Rondonia', 'sigla' => 'RO', 'codigo_ibge' => '11', 'regiao' => 'NORTE', 'ativo' => true],
            ['nome' => 'Roraima', 'sigla' => 'RR', 'codigo_ibge' => '14', 'regiao' => 'NORTE', 'ativo' => true],
            ['nome' => 'Santa Catarina', 'sigla' => 'SC', 'codigo_ibge' => '42', 'regiao' => 'SUL', 'ativo' => true],
            ['nome' => 'Sao Paulo', 'sigla' => 'SP', 'codigo_ibge' => '35', 'regiao' => 'SUDESTE', 'ativo' => true],
            ['nome' => 'Sergipe', 'sigla' => 'SE', 'codigo_ibge' => '28', 'regiao' => 'NORDESTE', 'ativo' => true],
            ['nome' => 'Tocantins', 'sigla' => 'TO', 'codigo_ibge' => '17', 'regiao' => 'NORTE', 'ativo' => true],
        ];

        DB::table('estados')->upsert(
            $rows,
            ['sigla'],
            ['nome', 'codigo_ibge', 'regiao', 'ativo'],
        );
    }
}
