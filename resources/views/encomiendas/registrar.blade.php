@extends('layouts.encomiendas')

@section('title', 'Registrar')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="package" :size="14" /> Nueva recepción</p>
    <h2 class="sec">Registrar una encomienda</h2>
    <p class="lede">Deja constancia de qué llegó y a qué colaborador de administrativa se le entrega. Al guardar se genera un código de seguimiento y su QR; administrativa se encargará de reasignarla a la dependencia final y notificar al interesado.</p>

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
                <label>Cédula del colaborador que recibe <span class="req">*</span></label>
                <input id="colaborador_cedula" name="colaborador_cedula" list="colaboradores-list" value="{{ old('colaborador_cedula') }}" placeholder="Número de cédula" required autocomplete="off">
                <datalist id="colaboradores-list">
                    @foreach($colaboradores as $col)
                        <option value="{{ $col->cedula }}" data-nombre="{{ $col->nombre }}" data-correo="{{ $col->correo }}">{{ $col->nombre }}</option>
                    @endforeach
                </datalist>
                <span class="hint">Si ya existe, el nombre y correo se completan solos. Si es nuevo, complétalos abajo.</span>
                @error('colaborador_cedula')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Nombre del colaborador <span class="req">*</span></label>
                <input id="colaborador_nombre" name="colaborador_nombre" value="{{ old('colaborador_nombre') }}" placeholder="Nombre de quien recibe en administrativa" required>
                @error('colaborador_nombre')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label>Correo del colaborador <span class="req">*</span></label>
                <input type="email" id="colaborador_correo" name="colaborador_correo" value="{{ old('colaborador_correo') }}" placeholder="nombre@uninavarra.edu.co" required>
                @error('colaborador_correo')<span class="error">{{ $message }}</span>@enderror
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

@section('scripts')
<script>
(function () {
    const cedula = document.getElementById('colaborador_cedula');
    const nombre = document.getElementById('colaborador_nombre');
    const correo = document.getElementById('colaborador_correo');
    const lista = document.getElementById('colaboradores-list');

    cedula.addEventListener('input', () => {
        const opcion = Array.from(lista.options).find(o => o.value === cedula.value);
        if (opcion) {
            nombre.value = opcion.dataset.nombre;
            correo.value = opcion.dataset.correo;
        }
    });
})();
</script>
@endsection
