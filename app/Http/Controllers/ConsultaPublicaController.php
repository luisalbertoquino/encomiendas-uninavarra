<?php

namespace App\Http\Controllers;

use App\Models\Encomienda;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultaPublicaController extends Controller
{
    public function form(Request $request): View
    {
        return view('consulta.form', [
            'documento' => '',
            'encomiendas' => null,
            'error' => null,
        ]);
    }

    public function buscar(Request $request): View
    {
        $data = $request->validate([
            'documento' => ['required', 'string', 'max:60'],
        ]);

        $documento = trim($data['documento']);

        $encomiendas = Encomienda::with('dependencia')
            ->where('documento_interesado', $documento)
            ->orderByRaw("FIELD(estado, 'recibida', 'notificada', 'entregada')")
            ->orderByDesc('fecha')
            ->get();

        return view('consulta.form', [
            'documento' => $documento,
            'encomiendas' => $encomiendas,
            'error' => $encomiendas->isEmpty() ? 'No encontramos encomiendas registradas con ese documento.' : null,
        ]);
    }
}
