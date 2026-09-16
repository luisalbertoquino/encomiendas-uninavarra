@extends('layouts.encomiendas')

@php
    $editando = $usuario->exists;
    $roles = ['recepcion' => 'Recepción', 'administrativa' => 'Administrativa', 'admin' => 'Administrador'];
@endphp

@section('title', $editando ? 'Editar usuario' : 'Nuevo usuario')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="user" :size="14" /> {{ $editando ? 'Editar' : 'Nuevo' }}</p>
    <h2 class="sec">{{ $editando ? 'Editar usuario' : 'Registrar nuevo usuario' }}</h2>
    <p class="lede">@if($editando) Actualiza el nombre, correo o rol de este usuario. @else Al guardar se generará una contraseña temporal que se mostrará una sola vez. @endif</p>

    <form method="POST" action="{{ $editando ? route('usuarios.update', $usuario) : route('usuarios.store') }}" class="card pad">
        @csrf
        @if($editando) @method('PUT') @endif
        <div class="grid">
            <div class="field">
                <label>Nombre <span class="req">*</span></label>
                <input name="name" value="{{ old('name', $usuario->name) }}" required>
                @error('name')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Correo <span class="req">*</span></label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                @error('email')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Rol <span class="req">*</span></label>
                <select name="role" required>
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $usuario->role) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role')<span class="error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary"><x-icon name="check-circle" :size="16" />Guardar</button>
            <a href="{{ route('usuarios.index') }}" class="btn ghost">Cancelar</a>
        </div>
    </form>
</section>
@endsection
