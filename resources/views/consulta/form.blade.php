<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consultar encomiendas · UNINAVARRA</title>
    <link rel="stylesheet" href="{{ asset('css/app-encomiendas.css') }}">
</head>
<body>
<div class="public-shell brand-bg">

<header class="public-header">
  <div class="wrap">
    <div class="brand-shield">
      <img src="{{ asset('images/logo-uninavarra.png') }}" alt="Escudo UNINAVARRA">
    </div>
    <div>
      <h1>Recepción de Encomiendas</h1>
      <div class="sub">UNINAVARRA · Neiva, Huila</div>
    </div>
    <div class="head-actions">
      <a href="{{ route('login') }}" class="btn ghost sm">
        <x-icon name="log-in" :size="16" />
        Ingresar
      </a>
    </div>
  </div>
</header>

<div class="hero">
  <div class="wrap" style="padding-bottom:0">
    <p class="eyebrow"><x-icon name="package" :size="14" /> Seguimiento de encomiendas</p>
    <h2>¿Tienes encomiendas pendientes?</h2>
    <p>Ingresa tu número de documento para ver todas las encomiendas registradas a tu nombre y su estado actual.</p>
    <div class="hero-actions">
      <a href="https://uninavarra.edu.co" target="_blank" rel="noopener" class="btn ghost sm">
        <x-icon name="building" :size="15" />
        Conocer más de UNINAVARRA
      </a>
    </div>
  </div>
</div>

<div class="consulta-wrap">
    <form method="POST" action="{{ route('consulta.buscar') }}" class="card pad">
        @csrf
        <div class="field" style="margin-bottom:16px">
            <label>Número de documento <span class="req">*</span></label>
            <input name="documento" value="{{ old('documento', $documento) }}" placeholder="Ej.: 1075234567" required autofocus>
            @error('documento')<span class="error">{{ $message }}</span>@enderror
        </div>
        <div class="actions" style="margin-top:4px">
            <button type="submit" class="btn primary">
                <x-icon name="search" :size="16" />
                Consultar mis encomiendas
            </button>
        </div>
    </form>

    @if($error)
        <div class="note">
            <x-icon name="clipboard-list" :size="17" />
            <span>{{ $error }}</span>
        </div>
    @endif

    @if($encomiendas && $encomiendas->isNotEmpty())
        @php
            $labels = ['recibida' => 'Recibida', 'notificada' => 'Notificada', 'entregada' => 'Entregada'];
            $order = ['recibida', 'notificada', 'entregada'];
        @endphp
        <p class="count" style="margin-top:24px">{{ $encomiendas->count() }} encomienda(s) encontrada(s)</p>
        @foreach($encomiendas as $encomienda)
            @php $estadoIdx = array_search($encomienda->estado, $order); @endphp
            <div class="card pad result-card">
                <div class="result-head">
                    <span class="code">{{ $encomienda->codigo }}</span>
                    <span class="badge {{ $encomienda->estado }}">{{ $labels[$encomienda->estado] }}</span>
                </div>
                <div class="result-desc">{{ $encomienda->descripcion }}</div>

                <div class="steps">
                    @foreach($order as $i => $k)
                        <span class="s {{ $i <= $estadoIdx ? 'on' : '' }}"><span class="dot"></span>{{ $labels[$k] }}</span>
                        @if($i < count($order) - 1)
                            <span class="bar {{ $i < $estadoIdx ? 'on' : '' }}"></span>
                        @endif
                    @endforeach
                </div>

                <dl class="kv">
                    <dt>Interesado</dt><dd>{{ $encomienda->interesado }}@if($encomienda->dependencia) · {{ $encomienda->dependencia->nombre }}@endif</dd>
                    <dt>Recibida</dt><dd>{{ $encomienda->fecha->format('d/m/Y H:i') }}</dd>
                    @if($encomienda->estado === 'entregada')
                    <dt>Entregada</dt><dd>{{ $encomienda->fecha_entrega?->format('d/m/Y H:i') }}</dd>
                    @else
                    <dt>Estado</dt><dd>Puedes reclamarla en recepción presentando tu documento.</dd>
                    @endif
                </dl>
            </div>
        @endforeach
    @endif
</div>

<footer class="public-foot">
    UNINAVARRA · Neiva, Huila
</footer>

</div>
</body>
</html>
