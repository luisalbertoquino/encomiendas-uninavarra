@extends('layouts.encomiendas')

@section('title', 'QR de la estación')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="qr-code" :size="14" /> Punto de recepción</p>
    <h2 class="sec">QR de la estación</h2>
    <p class="lede">Imprime este QR y pégalo en el mostrador. Al escanearlo se abre directamente el formulario de registro (requiere que hayas definido la URL de seguimiento en Ajustes).</p>
    <div class="card pad">
        <div class="poster">
            <div>
                <p class="eyebrow" style="color:var(--muted)">Escanea para registrar una entrega</p>
                <h2 class="sec" style="font-size:26px">Recepción de Encomiendas</h2>
                <p style="color:var(--muted);font-size:14px;margin:6px 0 0">UNINAVARRA · {{ $ajuste->estacion }}</p>
                @unless($ajuste->url)
                <div class="note">
                    <x-icon name="clipboard-list" :size="17" />
                    <span>Aún no has definido la <strong>URL de seguimiento</strong>. Ve a <strong>Ajustes</strong> y pégala para que el QR sea funcional.</span>
                </div>
                @endunless
            </div>
            <div class="qr-big" id="qrEstacion"></div>
        </div>
        <div class="no-print" style="margin-top:18px">
            <button class="btn ghost" onclick="window.print()"><x-icon name="printer" :size="16" />Imprimir cartel</button>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
const box = document.getElementById('qrEstacion');
@if($ajuste->url)
new QRCode(box, { text: @json($ajuste->url), width: 280, height: 280, correctLevel: QRCode.CorrectLevel.M });
@else
box.innerHTML = '<span style="color:var(--muted);font-size:12px;text-align:center;padding:20px">Define la URL en Ajustes para generar el QR</span>';
@endif
</script>
@endsection
