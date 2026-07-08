<?php

namespace Database\Seeders;

use App\Models\Dependencia;
use Illuminate\Database\Seeder;

class DependenciaSeeder extends Seeder
{
    public function run(): void
    {
        $dependencias = [
            'Rectoría',
            'Financiera',
            'Talento Humano',
            'Admisiones y Registro',
            'Biblioteca',
            'Sistemas / TIC',
            'Bienestar Universitario',
            'Programa de Derecho',
            'Programa de Administración',
            'Programa de Contaduría',
            'Extensión y Proyección Social',
        ];

        foreach ($dependencias as $nombre) {
            Dependencia::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
