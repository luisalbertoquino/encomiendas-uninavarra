<x-guest-layout>
    <p class="lede" style="margin-bottom:20px">¿Olvidaste tu contraseña? Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

    @if (session('status'))
        <div class="note" style="margin-top:0;margin-bottom:18px">
            <x-icon name="check-circle" :size="17" />
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field" style="margin-bottom:16px">
            <label for="email">Correo institucional</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="recepcion@uninavarra.edu.co">
            @error('email')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="actions" style="justify-content:flex-end">
            <button type="submit" class="btn primary">
                <x-icon name="mail" :size="16" />
                Enviar enlace de recuperación
            </button>
        </div>
    </form>
</x-guest-layout>
