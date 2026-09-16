<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    private const ROLES = ['recepcion', 'administrativa', 'admin'];

    public function index(): View
    {
        return view('usuarios.index', [
            'usuarios' => User::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('usuarios.formulario', [
            'usuario' => new User(),
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:'.implode(',', self::ROLES)],
        ]);

        $password = Str::password(16);

        $usuario = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => bcrypt($password),
        ]);
        $usuario->forceFill(['email_verified_at' => now()])->save();

        return redirect()
            ->route('usuarios.index')
            ->with('status', 'Usuario creado')
            ->with('password_generada', $password);
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.formulario', [
            'usuario' => $usuario,
            'roles' => self::ROLES,
        ]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$usuario->id],
            'role' => ['required', 'string', 'in:'.implode(',', self::ROLES)],
        ]);

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('status', 'Usuario actualizado');
    }

    public function resetPassword(User $usuario): RedirectResponse
    {
        $password = Str::password(16);

        $usuario->update(['password' => bcrypt($password)]);

        return redirect()
            ->route('usuarios.index')
            ->with('status', 'Contraseña restablecida')
            ->with('password_generada', $password);
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        abort_if($usuario->id === $request->user()->id, 422, 'No puedes eliminar tu propio usuario.');
        abort_if(
            $usuario->role === 'admin' && User::where('role', 'admin')->count() <= 1,
            422,
            'Debe existir al menos un administrador.'
        );

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('status', 'Usuario eliminado');
    }
}
