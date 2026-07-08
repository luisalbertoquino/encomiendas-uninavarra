@extends('layouts.encomiendas')

@section('title', 'Ajustes')

@section('content')
<section class="panel">
    <p class="eyebrow"><x-icon name="settings" :size="14" /> Configuración</p>
    <h2 class="sec">Ajustes</h2>
    <p class="lede">Personaliza la estación y define cómo apuntan los códigos QR.</p>

    <form method="POST" action="{{ route('encomiendas.ajustes.update') }}" class="card pad" style="margin-bottom:16px">
        @csrf @method('PUT')
        <div class="grid">
            <div class="field">
                <label>Nombre de la estación / sede</label>
                <input name="estacion" value="{{ old('estacion', $ajuste->estacion) }}" placeholder="Recepción principal">
            </div>
            <div class="field">
                <label>Prefijo del código</label>
                <input name="prefijo" value="{{ old('prefijo', $ajuste->prefijo) }}" maxlength="6" placeholder="UNV">
            </div>
            <div class="field full">
                <label>URL de seguimiento (donde está publicado el sistema)</label>
                <input name="url" value="{{ old('url', $ajuste->url) }}" placeholder="https://encomiendas.uninavarra.edu.co">
                <span class="hint">Si la defines, cada QR abrirá la página de consulta pública para que el interesado ingrese su documento. Si la dejas vacía, el QR contendrá los datos de la encomienda como texto.</span>
                @error('url')<span class="error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary"><x-icon name="check-circle" :size="16" />Guardar ajustes</button>
        </div>
    </form>

    <div class="card pad" style="margin-bottom:16px">
        <p class="eyebrow" style="color:var(--muted)">Datos</p>
        <div class="kv">
            <dt>Encomiendas registradas</dt><dd>{{ $total }}</dd>
            <dt>Almacenamiento</dt><dd>Base de datos MySQL (persistente)</dd>
        </div>
    </div>

    <div class="card pad">
        <p class="eyebrow" style="color:var(--muted)"><x-icon name="file-text" :size="14" /> Histórico</p>
        <h2 class="sec" style="font-size:18px;margin-bottom:4px">Descargar registro histórico</h2>
        <p class="lede" style="margin-bottom:18px">Exporta el detalle de todas las encomiendas en un archivo CSV (compatible con Excel), útil como soporte administrativo o de auditoría. Puedes acotar por fecha de recepción, o dejar vacío para descargar todo.</p>

        <form method="GET" action="{{ route('encomiendas.exportar') }}">
            <div class="grid" style="margin-bottom:20px">
                <div class="field">
                    <label>Desde</label>
                    <input type="date" name="desde">
                </div>
                <div class="field">
                    <label>Hasta</label>
                    <input type="date" name="hasta">
                </div>
            </div>
            <div class="actions" style="margin-top:0">
                <button type="submit" class="btn gold">
                    <x-icon name="clipboard-list" :size="16" />
                    Descargar histórico (CSV)
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
