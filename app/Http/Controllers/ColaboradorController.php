<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColaboradorController extends Controller
{
    public function index(): View
    {
        return view('colaboradores.index', [
            'colaboradores' => Colaborador::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('colaboradores.formulario', [
            'colaborador' => new Colaborador(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cedula' => ['required', 'string', 'max:30', 'unique:colaboradores,cedula'],
            'correo' => ['required', 'email', 'max:255'],
        ]);

        Colaborador::create($data);

        return redirect()->route('colaboradores.index')->with('status', 'Colaborador creado');
    }

    public function edit(Colaborador $colaborador): View
    {
        return view('colaboradores.formulario', [
            'colaborador' => $colaborador,
        ]);
    }

    public function update(Request $request, Colaborador $colaborador): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cedula' => ['required', 'string', 'max:30', 'unique:colaboradores,cedula,'.$colaborador->id],
            'correo' => ['required', 'email', 'max:255'],
        ]);

        $colaborador->update($data);

        return redirect()->route('colaboradores.index')->with('status', 'Colaborador actualizado');
    }

    public function destroy(Colaborador $colaborador): RedirectResponse
    {
        abort_if($colaborador->encomiendas()->exists(), 422, 'No se puede eliminar: tiene encomiendas asociadas.');

        $colaborador->delete();

        return redirect()->route('colaboradores.index')->with('status', 'Colaborador eliminado');
    }
}
