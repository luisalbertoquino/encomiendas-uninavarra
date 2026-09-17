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

            <div class="field buscador-wrap">
                <div class="label-row">
                    <label>Cédula del colaborador que recibe <span class="req">*</span></label>
                    <span class="info-tip" tabindex="0">
                        <i class="icono">i</i>
                        <span class="globo">Escribe al menos 2 caracteres para ver coincidencias. Si es nuevo, completa nombre y correo abajo.</span>
                    </span>
                </div>
                <input id="colaborador_cedula" name="colaborador_cedula" value="{{ old('colaborador_cedula') }}" placeholder="Escribe cédula o nombre…" required autocomplete="off">
                <div id="colaborador_sugerencias" class="sugerencias" hidden></div>
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
function activarBuscador(prefijo, tipo) {
    const cedula = document.getElementById(prefijo + '_cedula');
    const nombre = document.getElementById(prefijo + '_nombre');
    const correo = document.getElementById(prefijo + '_correo');
    const caja = document.getElementById(prefijo + '_sugerencias');
    let timer = null;

    function ocultar() {
        caja.hidden = true;
        caja.innerHTML = '';
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[c]));
    }

    function mostrar(items) {
        if (!items.length) { ocultar(); return; }
        caja.innerHTML = items.map(p =>
            `<div class="sugerencia" data-nombre="${escapeHtml(p.nombre)}" data-correo="${escapeHtml(p.correo||'')}" data-cedula="${escapeHtml(p.cedula)}">
                <span class="datos"><strong>${escapeHtml(p.nombre)}</strong><small>C.C. ${escapeHtml(p.cedula)}</small></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </div>`
        ).join('');
        caja.hidden = false;
    }

    cedula.addEventListener('input', () => {
        const q = cedula.value.trim();
        clearTimeout(timer);
        if (q.length < 2) { ocultar(); return; }
        timer = setTimeout(() => {
            fetch(`{{ url('/buscar-personas') }}/${tipo}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(mostrar)
                .catch(ocultar);
        }, 250);
    });

    caja.addEventListener('click', (ev) => {
        const item = ev.target.closest('.sugerencia');
        if (!item) return;
        cedula.value = item.dataset.cedula;
        nombre.value = item.dataset.nombre;
        if (correo) correo.value = item.dataset.correo;
        ocultar();
    });

    document.addEventListener('click', (ev) => {
        if (!ev.target.closest('.buscador-wrap')) ocultar();
    });
}

activarBuscador('colaborador', 'colaborador');
</script>
@endsection
