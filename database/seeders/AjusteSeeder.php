<?php

namespace Database\Seeders;

use App\Models\Ajuste;
use Illuminate\Database\Seeder;

class AjusteSeeder extends Seeder
{
    public function run(): void
    {
        Ajuste::firstOrCreate([], [
            'estacion' => 'Recepción principal',
            'prefijo' => 'UNV',
            'url' => config('app.url'),
        ]);
    }
}
