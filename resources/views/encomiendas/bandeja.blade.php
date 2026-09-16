@extends('layouts.encomiendas')

@section('title', 'Bandeja')

@php
    $labels = ['recibida' => 'Recibida', 'en_administrativa' => 'En administrativa', 'notificada' => 'Notificada', 'entregada' => 'Entregada'];
    $order = ['recibida', 'en_administrativa', 'notificada', 'entregada'];
@endphp

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="inbox" :size="14" /> Seguimiento</p>
    <h2 class="sec">Bandeja de encomiendas</h2>
    <p class="lede">Todo lo recibido, con su estado de custodia. Notifica al interesado, márcala como entregada cuando la reclame, o imprime el sticker con QR para pegar en el paquete.</p>

    <form method="GET" action="{{ route('encomiendas.index') }}" class="toolbar">
        <div class="search-wrap">
            <x-icon name="search" :size="16" />
            <input type="search" name="buscar" value="{{ $buscar }}" placeholder="Buscar por código, interesado, descripción, dependencia…" onchange="this.form.submit()">
        </div>
        <div class="chips">
            @foreach(['todas' => 'Todas', 'recibida' => 'Recibidas', 'en_administrativa' => 'En administrativa', 'notificada' => 'Notificadas', 'entregada' => 'Entregadas'] as $key => $label)
                <a class="chip {{ $filtro === $key ? 'active' : '' }}"
                   href="{{ route('encomiendas.index', array_filter(['filtro' => $key === 'todas' ? null : $key, 'buscar' => $buscar ?: null])) }}">{{ $label }}</a>
            @endforeach
        </div>
    </form>
    <div class="count">{{ $encomiendas->total() }} encomienda(s)</div>

    <div>
    @forelse($encomiendas as $e)
        @php
            $wa = preg_replace('/\D/', '', $e->whatsapp ?? '');
            if ($wa && strlen($wa) === 10) { $wa = '57'.$wa; }
            $qrPayload = $ajuste->url
                ? rtrim($ajuste->url, '/').'/'
                : "ENCOMIENDA {$e->codigo}\n{$e->descripcion}\nInteresado: {$e->interesado}\nRecibida: {$e->fecha->format('d/m/Y H:i')} por {$e->recibe}\nUNINAVARRA";
            $mensaje = $e->mensajeAviso($ajuste->estacion);
            $idx = $order['estado'] ?? 0;
            $estadoIdx = array_search($e->estado, $order);
        @endphp
        <div class="card enc" id="enc-{{ $e->id }}" @if((string)$highlight === (string)$e->id) style="outline:3px solid var(--primary)" @endif>
            <div class="body">
                <div>
                    <div class="result-head">
                        <span class="code">{{ $e->codigo }}</span>
                        <span class="badge {{ $e->estado }}">{{ $labels[$e->estado] }}</span>
                    </div>
                    <div class="desc">{{ $e->descripcion }}</div>
                    <div style="font-size:12.5px;color:var(--muted)">{{ $e->tipo }}@if($e->remitente) · de {{ $e->remitente }}@endif</div>

                    <div class="steps">
                        @foreach($order as $i => $k)
                            <span class="s {{ $i <= $estadoIdx ? 'on' : '' }}"><span class="dot"></span>{{ $labels[$k] }}</span>
                            @if($i < count($order) - 1)
                                <span class="bar {{ $i < $estadoIdx ? 'on' : '' }}"></span>
                            @endif
                        @endforeach
                    </div>

                    <dl>
                        <dt>Interesado</dt><dd>{{ $e->interesado }}@if($e->dependencia) · {{ $e->dependencia->nombre }}@endif</dd>
                        <dt>Recibida</dt><dd>{{ $e->fecha->format('d/m/Y H:i') }} por {{ $e->colaborador?->nombre ?? $e->recibe }}</dd>
                        @if($e->guia)<dt>Guía</dt><dd>{{ $e->guia }}</dd>@endif
                        @if($e->whatsapp)<dt>WhatsApp</dt><dd>{{ $e->whatsapp }}</dd>@endif
                        @if($e->correo)<dt>Correo</dt><dd>{{ $e->correo }}</dd>@endif
                        @if($e->enlace_drive)<dt>Enlace</dt><dd><a href="{{ $e->enlace_drive }}" target="_blank" rel="noopener">Ver en Drive</a></dd>@endif
                        @if($e->obs)<dt>Obs.</dt><dd>{{ $e->obs }}</dd>@endif
                        @if($e->reasignada_at)<dt>Reasignada</dt><dd>{{ $e->reasignada_at->format('d/m/Y H:i') }} por {{ $e->reasignada_por }}</dd>@endif
                        @if($e->estado === 'entregada')<dt>Entregada</dt><dd>{{ $e->fecha_entrega?->format('d/m/Y H:i') }} a {{ $e->entregado_a ?: $e->interesado }}</dd>@endif
                    </dl>

                    @if($e->estado === 'recibida' && auth()->user()->isAdministrativa())
                    <form method="POST" action="{{ route('encomiendas.reasignar', $e) }}" class="card pad reasignar-form">
                        @csrf
                        <div class="grid">
                            <div class="field">
                                <label>Dependencia final <span class="req">*</span></label>
                                <select name="dependencia_id" required>
                                    <option value="">— Seleccione —</option>
                                    @foreach($dependencias as $dep)
                                        <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Correo institucional destino <span class="req">*</span></label>
                                <input type="email" name="correo" placeholder="dependencia@uninavarra.edu.co" required>
                            </div>
                        </div>
                        <div class="actions">
                            <button type="submit" class="btn primary sm"><x-icon name="arrow-right" :size="15" />Tomar custodia y reasignar</button>
                        </div>
                    </form>
                    @endif
                </div>
                <div class="qr-box">
                    <div class="qr" data-qr="{{ $qrPayload }}"></div>
                    <small>{{ $ajuste->url ? 'Escanea para seguir' : 'Datos de la encomienda' }}</small>
                </div>
            </div>
            <div class="foot no-print">
                @if($wa)
                    <a class="btn wa sm" target="_blank" rel="noopener"
                       href="https://wa.me/{{ $wa }}?text={{ urlencode($mensaje) }}"><x-icon name="message-circle" :size="15" />Avisar por WhatsApp</a>
                @endif
                @if($e->estado === 'notificada' && $e->correo)
                    <form method="POST" action="{{ route('encomiendas.notificar', $e) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn mail sm"><x-icon name="mail" :size="15" />Reenviar correo</button>
                    </form>
                @endif
                <span class="grow"></span>
                <button type="button" class="btn ghost sm" onclick="imprimir({{ $e->id }})"><x-icon name="printer" :size="15" />Imprimir sticker</button>
                @if($e->estado !== 'entregada')
                <form method="POST" action="{{ route('encomiendas.entregar', $e) }}" onsubmit="return marcarEntregada(event, this, '{{ $e->interesado }}')" style="display:inline">
                    @csrf
                    <input type="hidden" name="entregado_a" value="">
                    <button type="submit" class="btn done sm"><x-icon name="check-circle" :size="15" />Marcar entregada</button>
                </form>
                @endif
                <form method="POST" action="{{ route('encomiendas.destroy', $e) }}" onsubmit="return confirm('¿Eliminar la encomienda {{ $e->codigo }}? Esta acción no se puede deshacer.')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn del sm"><x-icon name="trash" :size="15" />Eliminar</button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty card">
            <x-icon name="package" :size="44" />
            <p>No hay encomiendas {{ $buscar || $filtro !== 'todas' ? 'con ese criterio' : 'registradas aún' }}.</p>
        </div>
    @endforelse
    </div>

    <div class="no-print" style="margin-top:20px">{{ $encomiendas->links() }}</div>
</section>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.qr').forEach(el => {
    new QRCode(el, { text: el.dataset.qr, width: 160, height: 160, correctLevel: QRCode.CorrectLevel.M });
});

function marcarEntregada(ev, form, sugerido) {
    const quien = prompt('¿A quién se le entrega? (nombre de quien reclama)', sugerido);
    if (quien === null) { ev.preventDefault(); return false; }
    form.querySelector('input[name=entregado_a]').value = quien.trim();
    return true;
}

function imprimir(id) {
    document.querySelectorAll('.enc').forEach(c => c.classList.toggle('print-me', c.id === 'enc-'+id));
    window.print();
}
</script>
@endsection
