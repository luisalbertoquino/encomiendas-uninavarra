@extends('layouts.encomiendas')

@section('title', 'Colaboradores')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="user" :size="14" /> Catálogo</p>
    <h2 class="sec">Colaboradores</h2>
    <p class="lede">Colaboradores del área administrativa que reciben encomiendas en recepción. Se crean automáticamente al registrar una encomienda con una cédula nueva, o puedes gestionarlos aquí directamente.</p>

    <div class="actions" style="margin-bottom:16px">
        <a href="{{ route('colaboradores.create') }}" class="btn primary"><x-icon name="user" :size="16" />Nuevo colaborador</a>
    </div>

    <div class="card pad">
        @forelse($colaboradores as $c)
        <div class="result-head" style="padding:10px 0;border-bottom:1px solid var(--border)">
            <div>
                <strong>{{ $c->nombre }}</strong>
                <div style="font-size:12.5px;color:var(--muted)">C.C. {{ $c->cedula }} · {{ $c->correo }}</div>
            </div>
            <div class="actions" style="margin:0">
                <a href="{{ route('colaboradores.edit', $c) }}" class="btn ghost sm">Editar</a>
                <form method="POST" action="{{ route('colaboradores.destroy', $c) }}" onsubmit="return confirm('¿Eliminar al colaborador {{ $c->nombre }}?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn del sm"><x-icon name="trash" :size="15" />Eliminar</button>
                </form>
            </div>
        </div>
        @empty
        <p class="lede">No hay colaboradores registrados.</p>
        @endforelse
    </div>
</section>
@endsection
