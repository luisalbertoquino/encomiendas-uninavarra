<x-guest-layout>
    @if (session('status'))
        <div class="note" style="margin-top:0;margin-bottom:18px">
            <x-icon name="check-circle" :size="17" />
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field" style="margin-bottom:16px">
            <label for="email">Correo institucional</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="recepcion@uninavarra.edu.co">
            @error('email')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="field" style="margin-bottom:10px">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')<span class="error">{{ $message }}</span>@enderror
        </div>

        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);font-weight:400;margin:14px 0 4px;cursor:pointer">
            <input type="checkbox" name="remember" style="width:auto">
            Mantener la sesión iniciada
        </label>

        <div class="actions" style="justify-content:space-between;align-items:center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--muted);text-decoration:none">¿Olvidaste tu contraseña?</a>
            @endif
            <button type="submit" class="btn primary">
                <x-icon name="log-in" :size="16" />
                Ingresar
            </button>
        </div>
    </form>
</x-guest-layout>
