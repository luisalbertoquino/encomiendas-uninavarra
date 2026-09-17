<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusquedaPersonaController extends Controller
{
    private const MODELOS = [
        'colaborador' => Colaborador::class,
    ];

    public function buscar(Request $request, string $tipo): JsonResponse
    {
        abort_unless(array_key_exists($tipo, self::MODELOS), 404);

        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $modelo = self::MODELOS[$tipo];

        $resultados = $modelo::where('cedula', 'like', "{$q}%")
            ->orWhere('nombre', 'like', "%{$q}%")
            ->orderBy('nombre')
            ->limit(8)
            ->get(['id', 'nombre', 'cedula', 'correo']);

        return response()->json($resultados);
    }
}
