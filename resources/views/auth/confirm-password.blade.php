<x-guest-layout>
    <p class="lede" style="margin-bottom:20px">Esta es un área protegida. Confirma tu contraseña antes de continuar.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="field" style="margin-bottom:10px">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="actions" style="justify-content:flex-end">
            <button type="submit" class="btn primary">
                <x-icon name="shield-check" :size="16" />
                Confirmar
            </button>
        </div>
    </form>
</x-guest-layout>
