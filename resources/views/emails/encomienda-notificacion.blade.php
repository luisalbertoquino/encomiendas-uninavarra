<x-mail::message>
# Llegó tu encomienda

Hola **{{ $encomienda->interesado }}**,

Te informamos desde UNINAVARRA ({{ $estacion }}) que llegó tu encomienda a recepción.

<x-mail::panel>
**Código de seguimiento:** {{ $encomienda->codigo }}<br>
**Descripción:** {{ $encomienda->descripcion }}<br>
**Recibida:** {{ $encomienda->fecha->format('d/m/Y H:i') }} por {{ $encomienda->recibe }}
</x-mail::panel>

Puedes reclamarla en recepción presentando tu documento de identidad.

<x-mail::button :url="route('consulta.form')">
Consultar mis encomiendas
</x-mail::button>

Gracias,<br>
UNINAVARRA · Recepción de Encomiendas
</x-mail::message>
