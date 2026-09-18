<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DependenciaController extends Controller
{
    public function index(): View
    {
        return view('dependencias.index', [
            'dependencias' => Dependencia::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('dependencias.formulario', [
            'dependencia' => new Dependencia(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:dependencias,nombre'],
            'correo' => ['nullable', 'email', 'max:255'],
            'nombre_interesado' => ['nullable', 'string', 'max:255'],
            'cedula_interesado' => ['nullable', 'string', 'max:30'],
            'telefono_interesado' => ['nullable', 'string', 'max:20'],
        ]);

        Dependencia::create($data);

        return redirect()->route('dependencias.index')->with('status', 'Dependencia creada');
    }

    public function edit(Dependencia $dependencia): View
    {
        return view('dependencias.formulario', [
            'dependencia' => $dependencia,
        ]);
    }

    public function update(Request $request, Dependencia $dependencia): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:dependencias,nombre,'.$dependencia->id],
            'correo' => ['nullable', 'email', 'max:255'],
            'nombre_interesado' => ['nullable', 'string', 'max:255'],
            'cedula_interesado' => ['nullable', 'string', 'max:30'],
            'telefono_interesado' => ['nullable', 'string', 'max:20'],
        ]);

        $dependencia->update($data);

        return redirect()->route('dependencias.index')->with('status', 'Dependencia actualizada');
    }

    public function destroy(Dependencia $dependencia): RedirectResponse
    {
        abort_if($dependencia->encomiendas()->exists(), 422, 'No se puede eliminar: tiene encomiendas asociadas.');

        $dependencia->delete();

        return redirect()->route('dependencias.index')->with('status', 'Dependencia eliminada');
    }
}
