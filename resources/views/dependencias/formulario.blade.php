@extends('layouts.encomiendas')

@php
    $editando = $dependencia->exists;
@endphp

@section('title', $editando ? 'Editar dependencia' : 'Nueva dependencia')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="building" :size="14" /> {{ $editando ? 'Editar' : 'Nueva' }}</p>
    <h2 class="sec">{{ $editando ? 'Editar dependencia' : 'Registrar nueva dependencia' }}</h2>

    <form method="POST" action="{{ $editando ? route('dependencias.update', $dependencia) : route('dependencias.store') }}" class="card pad">
        @csrf
        @if($editando) @method('PUT') @endif
        <div class="grid">
            <div class="field">
                <label>Nombre <span class="req">*</span></label>
                <input name="nombre" value="{{ old('nombre', $dependencia->nombre) }}" required>
                @error('nombre')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Correo institucional</label>
                <input type="email" name="correo" value="{{ old('correo', $dependencia->correo) }}" placeholder="dependencia@uninavarra.edu.co">
                <span class="hint">Se autocompletará en el formulario de reasignación al seleccionar esta dependencia.</span>
                @error('correo')<span class="error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary"><x-icon name="check-circle" :size="16" />Guardar</button>
            <a href="{{ route('dependencias.index') }}" class="btn ghost">Cancelar</a>
        </div>
    </form>
</section>
@endsection
