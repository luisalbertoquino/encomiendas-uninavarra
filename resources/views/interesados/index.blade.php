@extends('layouts.encomiendas')

@section('title', 'Interesados')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="user" :size="14" /> Catálogo</p>
    <h2 class="sec">Interesados</h2>
    <p class="lede">Personas que reciben encomiendas. Se crean automáticamente al registrar una encomienda con una cédula nueva, o puedes gestionarlas aquí directamente.</p>

    <div class="actions" style="margin-bottom:16px">
        <a href="{{ route('interesados.create') }}" class="btn primary"><x-icon name="user" :size="16" />Nuevo interesado</a>
    </div>

    <div class="card pad">
        @forelse($interesados as $i)
        <div class="result-head" style="padding:10px 0;border-bottom:1px solid var(--border)">
            <div>
                <strong>{{ $i->nombre }}</strong>
                <div style="font-size:12.5px;color:var(--muted)">C.C. {{ $i->cedula }}@if($i->correo) · {{ $i->correo }}@endif</div>
            </div>
            <div class="actions" style="margin:0">
                <a href="{{ route('interesados.edit', $i) }}" class="btn ghost sm">Editar</a>
                <form method="POST" action="{{ route('interesados.destroy', $i) }}" onsubmit="return confirm('¿Eliminar al interesado {{ $i->nombre }}?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn del sm"><x-icon name="trash" :size="15" />Eliminar</button>
                </form>
            </div>
        </div>
        @empty
        <p class="lede">No hay interesados registrados.</p>
        @endforelse
    </div>
</section>
@endsection
