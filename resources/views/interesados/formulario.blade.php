@extends('layouts.encomiendas')

@php
    $editando = $interesado->exists;
@endphp

@section('title', $editando ? 'Editar interesado' : 'Nuevo interesado')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="user" :size="14" /> {{ $editando ? 'Editar' : 'Nuevo' }}</p>
    <h2 class="sec">{{ $editando ? 'Editar interesado' : 'Registrar nuevo interesado' }}</h2>

    <form method="POST" action="{{ $editando ? route('interesados.update', $interesado) : route('interesados.store') }}" class="card pad">
        @csrf
        @if($editando) @method('PUT') @endif
        <div class="grid">
            <div class="field">
                <label>Nombre <span class="req">*</span></label>
                <input name="nombre" value="{{ old('nombre', $interesado->nombre) }}" required>
                @error('nombre')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Cédula <span class="req">*</span></label>
                <input name="cedula" value="{{ old('cedula', $interesado->cedula) }}" required>
                @error('cedula')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Correo</label>
                <input type="email" name="correo" value="{{ old('correo', $interesado->correo) }}">
                @error('correo')<span class="error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary"><x-icon name="check-circle" :size="16" />Guardar</button>
            <a href="{{ route('interesados.index') }}" class="btn ghost">Cancelar</a>
        </div>
    </form>
</section>
@endsection
