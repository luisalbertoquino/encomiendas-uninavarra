<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field" style="margin-bottom:16px">
            <label for="email">Correo institucional</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="field" style="margin-bottom:16px">
            <label for="password">Nueva contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="field" style="margin-bottom:10px">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="actions" style="justify-content:flex-end">
            <button type="submit" class="btn primary">
                <x-icon name="lock" :size="16" />
                Restablecer contraseña
            </button>
        </div>
    </form>
</x-guest-layout>
