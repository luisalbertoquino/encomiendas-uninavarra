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
                <span class="hint">Se usará para notificar al interesado al reasignar una encomienda a esta dependencia.</span>
                @error('correo')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Nombre del interesado / contacto</label>
                <input name="nombre_interesado" value="{{ old('nombre_interesado', $dependencia->nombre_interesado) }}" placeholder="Nombre de quien recibe en esta dependencia">
                @error('nombre_interesado')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Cédula del interesado / contacto</label>
                <input name="cedula_interesado" value="{{ old('cedula_interesado', $dependencia->cedula_interesado) }}">
                @error('cedula_interesado')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field full">
                <label>Enlace de soporte digital (si aplica)</label>
                <input type="url" name="enlace_drive" value="{{ old('enlace_drive', $dependencia->enlace_drive) }}" placeholder="https://...">
                <span class="hint">No se suben archivos al servidor: pega aquí el enlace donde ya lo hayas compartido (Google Drive, OneDrive, Dropbox, etc.). Se copiará a cada encomienda reasignada a esta dependencia.</span>
                @error('enlace_drive')<span class="error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary"><x-icon name="check-circle" :size="16" />Guardar</button>
            <a href="{{ route('dependencias.index') }}" class="btn ghost">Cancelar</a>
        </div>
    </form>
</section>
@endsection
