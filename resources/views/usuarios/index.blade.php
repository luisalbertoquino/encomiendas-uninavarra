@extends('layouts.encomiendas')

@section('title', 'Usuarios')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="user" :size="14" /> Administración</p>
    <h2 class="sec">Usuarios</h2>
    <p class="lede">Crea, edita y administra el acceso de recepción, administrativa y otros administradores.</p>

    @if(session('password_generada'))
    <div class="card pad" style="margin-bottom:16px;border:2px solid var(--primary)">
        <p class="eyebrow" style="color:var(--primary)"><x-icon name="lock" :size="14" /> Contraseña generada</p>
        <p class="lede" style="margin-bottom:8px">Copia esta contraseña ahora, no se volverá a mostrar:</p>
        <code style="font-size:18px;font-weight:600;user-select:all">{{ session('password_generada') }}</code>
    </div>
    @endif

    <div class="actions" style="margin-bottom:16px">
        <a href="{{ route('usuarios.create') }}" class="btn primary"><x-icon name="user" :size="16" />Nuevo usuario</a>
    </div>

    <div class="card pad">
        @forelse($usuarios as $u)
        <div class="result-head" style="padding:10px 0;border-bottom:1px solid var(--border)">
            <div>
                <strong>{{ $u->name }}</strong>
                <div style="font-size:12.5px;color:var(--muted)">{{ $u->email }} · {{ ucfirst($u->role) }}</div>
            </div>
            <div class="actions" style="margin:0">
                <a href="{{ route('usuarios.edit', $u) }}" class="btn ghost sm">Editar</a>
                <form method="POST" action="{{ route('usuarios.reset-password', $u) }}" onsubmit="return confirm('¿Restablecer la contraseña de {{ $u->name }}?')" style="display:inline">
                    @csrf
                    <button type="submit" class="btn ghost sm"><x-icon name="lock" :size="15" />Restablecer contraseña</button>
                </form>
                <form method="POST" action="{{ route('usuarios.destroy', $u) }}" onsubmit="return confirm('¿Eliminar el usuario {{ $u->name }}? Esta acción no se puede deshacer.')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn del sm"><x-icon name="trash" :size="15" />Eliminar</button>
                </form>
            </div>
        </div>
        @empty
        <p class="lede">No hay usuarios registrados.</p>
        @endforelse
    </div>
</section>
@endsection
