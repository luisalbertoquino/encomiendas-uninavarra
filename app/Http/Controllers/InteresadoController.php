<?php

namespace App\Http\Controllers;

use App\Models\Interesado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InteresadoController extends Controller
{
    public function index(): View
    {
        return view('interesados.index', [
            'interesados' => Interesado::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('interesados.formulario', [
            'interesado' => new Interesado(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cedula' => ['required', 'string', 'max:30', 'unique:interesados,cedula'],
            'correo' => ['nullable', 'email', 'max:255'],
        ]);

        Interesado::create($data);

        return redirect()->route('interesados.index')->with('status', 'Interesado creado');
    }

    public function edit(Interesado $interesado): View
    {
        return view('interesados.formulario', [
            'interesado' => $interesado,
        ]);
    }

    public function update(Request $request, Interesado $interesado): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cedula' => ['required', 'string', 'max:30', 'unique:interesados,cedula,'.$interesado->id],
            'correo' => ['nullable', 'email', 'max:255'],
        ]);

        $interesado->update($data);

        return redirect()->route('interesados.index')->with('status', 'Interesado actualizado');
    }

    public function destroy(Interesado $interesado): RedirectResponse
    {
        abort_if($interesado->encomiendas()->exists(), 422, 'No se puede eliminar: tiene encomiendas asociadas.');

        $interesado->delete();

        return redirect()->route('interesados.index')->with('status', 'Interesado eliminado');
    }
}
