@extends('layouts.encomiendas')

@section('title', 'Registrar')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="package" :size="14" /> Nueva recepción</p>
    <h2 class="sec">Registrar una encomienda</h2>
    <p class="lede">Deja constancia de qué llegó, quién la recibió y a quién pertenece. Al guardar se genera un código de seguimiento y su QR, y podrás avisarle al interesado en un toque.</p>

    <form method="POST" action="{{ route('encomiendas.store') }}" class="card pad">
        @csrf
        <div class="grid">
            <div class="field">
                <label>Fecha y hora de llegada <span class="req">*</span></label>
                <input type="datetime-local" name="fecha" value="{{ old('fecha', now()->format('Y-m-d\TH:i')) }}" required>
                @error('fecha')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Tipo de encomienda</label>
                <select name="tipo">
                    @foreach(['Paquete','Sobre / correspondencia','Documento','Insumo / material','Equipo','Otro'] as $tipo)
                        <option value="{{ $tipo }}" @selected(old('tipo')===$tipo)>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field full">
                <label>¿Qué es? — descripción <span class="req">*</span></label>
                <input name="descripcion" value="{{ old('descripcion') }}" placeholder="Ej.: Caja mediana con libros, remitente Editorial Legis" required>
                @error('descripcion')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Remitente / origen</label>
                <input name="remitente" value="{{ old('remitente') }}" placeholder="Empresa o persona que envía">
            </div>
            <div class="field">
                <label>Guía / transportadora (opcional)</label>
                <input name="guia" value="{{ old('guia') }}" placeholder="N.º de guía, Servientrega, etc.">
            </div>
            <div class="field">
                <label>¿Quién la recibe? <span class="req">*</span></label>
                <input name="recibe" value="{{ old('recibe') }}" placeholder="Nombre del funcionario de recepción" required>
                @error('recibe')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Interesado / destinatario <span class="req">*</span></label>
                <input name="interesado" value="{{ old('interesado') }}" placeholder="Nombre de quien espera la encomienda" required>
                @error('interesado')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Documento del interesado <span class="req">*</span></label>
                <input name="documento_interesado" value="{{ old('documento_interesado') }}" placeholder="Cédula" required>
                <span class="hint">Con este número el interesado podrá consultar todas sus encomiendas pendientes, sin necesidad de cuenta.</span>
                @error('documento_interesado')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Dependencia / oficina</label>
                <select name="dependencia_id">
                    <option value="">— Sin especificar —</option>
                    @foreach($dependencias as $dep)
                        <option value="{{ $dep->id }}" @selected((string) old('dependencia_id') === (string) $dep->id)>{{ $dep->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>WhatsApp del interesado</label>
                <input name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Ej.: 3001234567" inputmode="tel">
                <span class="hint">Solo dígitos. Se asume Colombia (+57) si no pones indicativo.</span>
            </div>
            <div class="field">
                <label>Correo del interesado</label>
                <input type="email" name="correo" value="{{ old('correo') }}" placeholder="nombre@uninavarra.edu.co">
                @error('correo')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field full">
                <label>Enlace de soporte digital (si aplica)</label>
                <input type="url" name="enlace_drive" value="{{ old('enlace_drive') }}" placeholder="https://...">
                <span class="hint">No se suben archivos al servidor: pega aquí el enlace donde ya lo hayas compartido (Google Drive, OneDrive, Dropbox, etc.).</span>
                @error('enlace_drive')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field full">
                <label>Observaciones</label>
                <textarea name="obs" placeholder="Estado del empaque, condiciones especiales, etc.">{{ old('obs') }}</textarea>
            </div>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary"><x-icon name="qr-code" :size="16" />Guardar y generar QR</button>
            <a href="{{ route('encomiendas.create') }}" class="btn ghost">Limpiar</a>
        </div>
    </form>
</section>
@endsection
