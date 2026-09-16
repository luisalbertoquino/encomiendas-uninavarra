<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Recepción de Encomiendas') · UNINAVARRA</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app-encomiendas.css') }}">
</head>
<body class="preload">
<script>requestAnimationFrame(()=>document.body.classList.remove('preload'));</script>

<header class="top">
  <div class="wrap">
    <div class="head-row">
      <div class="brand">
        <h1>Recepción de Encomiendas</h1>
      </div>
      <div class="brand-shield brand-shield-center">
        <img src="{{ asset('images/logo-uninavarra.png') }}" alt="Escudo UNINAVARRA">
      </div>
      <div class="head-actions">
        <span class="estacion-pill">
          <x-icon name="building" :size="14" />
          <span class="label">{{ $ajuste->estacion ?? 'Recepción principal' }}</span>
        </span>
        <form method="POST" action="{{ route('logout') }}" class="no-print btn-logout">
          @csrf
          <button type="submit">
            <x-icon name="log-out" :size="14" />
            <span>{{ auth()->user()->name }}</span>
          </button>
        </form>
      </div>
    </div>
    <nav class="tabs">
      @if(auth()->user()->role === 'recepcion' || auth()->user()->isAdmin())
      <a href="{{ route('encomiendas.create') }}" class="{{ request()->routeIs('encomiendas.create') ? 'active' : '' }}">
        <x-icon name="package" :size="16" /> <span class="label">Registrar</span>
      </a>
      @endif
      @if(auth()->user()->isAdministrativa() || auth()->user()->isAdmin())
      <a href="{{ route('encomiendas.index') }}" class="{{ request()->routeIs('encomiendas.index') ? 'active' : '' }}">
        <x-icon name="inbox" :size="16" /> <span class="label">Bandeja</span>
      </a>
      @endif
      <a href="{{ route('encomiendas.estacion') }}" class="{{ request()->routeIs('encomiendas.estacion') ? 'active' : '' }}">
        <x-icon name="qr-code" :size="16" /> <span class="label">QR de la estación</span>
      </a>
      @if(auth()->user()->isAdministrativa() || auth()->user()->isAdmin())
      <a href="{{ route('encomiendas.ajustes') }}" class="{{ request()->routeIs('encomiendas.ajustes') ? 'active' : '' }}">
        <x-icon name="settings" :size="16" /> <span class="label">Ajustes</span>
      </a>
      @endif
      @if(auth()->user()->isAdmin())
      <a href="{{ route('dependencias.index') }}" class="{{ request()->routeIs('dependencias.*') ? 'active' : '' }}">
        <x-icon name="building" :size="16" /> <span class="label">Dependencias</span>
      </a>
      <a href="{{ route('colaboradores.index') }}" class="{{ request()->routeIs('colaboradores.*') ? 'active' : '' }}">
        <x-icon name="user" :size="16" /> <span class="label">Colaboradores</span>
      </a>
      <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
        <x-icon name="user" :size="16" /> <span class="label">Usuarios</span>
      </a>
      @endif
    </nav>
  </div>
</header>

<div class="wrap">
    @yield('content')
</div>

@if (session('status'))
<div class="toast show" id="toast"><x-icon name="check-circle" :size="16" />{{ session('status') }}</div>
<script>setTimeout(()=>document.getElementById('toast').classList.remove('show'),2600);</script>
@endif

@yield('scripts')
</body>
</html>
