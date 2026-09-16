<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Encomiendas UNINAVARRA') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app-encomiendas.css') }}">
</head>
<body>
<div class="auth-shell brand-bg">
    <div class="auth-card">
        <div class="auth-shield">
            <img src="{{ asset('images/logo-uninavarra.png') }}" alt="Escudo UNINAVARRA">
        </div>
        <div class="auth-title">
            <h1>Recepción de Encomiendas</h1>
        </div>
        <div class="card pad">
            {{ $slot }}
        </div>
        <div class="auth-back">
            <a href="{{ route('consulta.form') }}">
                <x-icon name="arrow-right" :size="14" style="transform:rotate(180deg)" />
                Volver a consultar una encomienda
            </a>
        </div>
    </div>
</div>
</body>
</html>
