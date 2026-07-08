<x-guest-layout>
    <p class="lede" style="margin-bottom:20px">Antes de continuar, confirma tu correo haciendo clic en el enlace que te enviamos. Si no lo recibiste, con gusto te enviamos otro.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="note" style="margin-top:0;margin-bottom:18px">
            <x-icon name="check-circle" :size="17" />
            <span>Enviamos un nuevo enlace de verificación al correo que registraste.</span>
        </div>
    @endif

    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn primary">
                <x-icon name="mail" :size="16" />
                Reenviar verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn ghost sm">
                <x-icon name="log-out" :size="14" />
                Salir
            </button>
        </form>
    </div>
</x-guest-layout>
