@extends('layouts.encomiendas')

@section('title', 'Dependencias')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="building" :size="14" /> Catálogo</p>
    <h2 class="sec">Dependencias</h2>
    <p class="lede">Administra las dependencias de destino y el interesado/contacto fijo que las recibe. Al reasignar una encomienda, estos datos se copian automáticamente.</p>

    <div class="actions" style="margin-bottom:16px">
        <a href="{{ route('dependencias.create') }}" class="btn primary"><x-icon name="building" :size="16" />Nueva dependencia</a>
    </div>

    <div class="card pad">
        @forelse($dependencias as $d)
        <div class="result-head" style="padding:10px 0;border-bottom:1px solid var(--border)">
            <div>
                <strong>{{ $d->nombre }}</strong>
                <div style="font-size:12.5px;color:var(--muted)">
                    @if($d->nombre_interesado)
                        {{ $d->nombre_interesado }} · {{ $d->correo ?: 'sin correo' }}
                    @else
                        <span style="color:var(--danger)">Sin interesado configurado</span>
                    @endif
                </div>
            </div>
            <div class="actions" style="margin:0">
                <a href="{{ route('dependencias.edit', $d) }}" class="btn ghost sm">Editar</a>
                <form method="POST" action="{{ route('dependencias.destroy', $d) }}" onsubmit="return confirm('¿Eliminar la dependencia {{ $d->nombre }}?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn del sm"><x-icon name="trash" :size="15" />Eliminar</button>
                </form>
            </div>
        </div>
        @empty
        <p class="lede">No hay dependencias registradas.</p>
        @endforelse
    </div>
</section>
@endsection
