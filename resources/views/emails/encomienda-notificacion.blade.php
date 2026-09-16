<x-mail::message>
# Llegó tu encomienda

Hola **{{ $encomienda->interesado }}**,

Te informamos desde UNINAVARRA ({{ $estacion }}) que tu encomienda ya está disponible en **{{ $encomienda->dependencia?->nombre }}**.

<x-mail::panel>
**Código de seguimiento:** {{ $encomienda->codigo }}<br>
**Descripción:** {{ $encomienda->descripcion }}<br>
**Recibida:** {{ $encomienda->fecha->format('d/m/Y H:i') }}<br>
**Dependencia:** {{ $encomienda->dependencia?->nombre }}
</x-mail::panel>

Puedes reclamarla presentando tu documento de identidad.

<x-mail::button :url="route('consulta.form')">
Consultar mis encomiendas
</x-mail::button>

Gracias,<br>
UNINAVARRA · Recepción de Encomiendas
</x-mail::message>
