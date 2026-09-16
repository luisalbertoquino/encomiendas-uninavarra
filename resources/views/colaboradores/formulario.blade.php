@extends('layouts.encomiendas')

@php
    $editando = $colaborador->exists;
@endphp

@section('title', $editando ? 'Editar colaborador' : 'Nuevo colaborador')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="user" :size="14" /> {{ $editando ? 'Editar' : 'Nuevo' }}</p>
    <h2 class="sec">{{ $editando ? 'Editar colaborador' : 'Registrar nuevo colaborador' }}</h2>

    <form method="POST" action="{{ $editando ? route('colaboradores.update', $colaborador) : route('colaboradores.store') }}" class="card pad">
        @csrf
        @if($editando) @method('PUT') @endif
        <div class="grid">
            <div class="field">
                <label>Nombre <span class="req">*</span></label>
                <input name="nombre" value="{{ old('nombre', $colaborador->nombre) }}" required>
                @error('nombre')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Cédula <span class="req">*</span></label>
                <input name="cedula" value="{{ old('cedula', $colaborador->cedula) }}" required>
                @error('cedula')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Correo <span class="req">*</span></label>
                <input type="email" name="correo" value="{{ old('correo', $colaborador->correo) }}" required>
                @error('correo')<span class="error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary"><x-icon name="check-circle" :size="16" />Guardar</button>
            <a href="{{ route('colaboradores.index') }}" class="btn ghost">Cancelar</a>
        </div>
    </form>
</section>
@endsection
