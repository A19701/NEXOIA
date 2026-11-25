<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXOIA | Acceso</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #090d16;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        .contenedor {
            width: 100%;
            max-width: 420px;
        }

        .marca {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            background: #6366f1;
            border-radius: 16px;
            width: 55px;
            height: 55px;
            display: grid;
            place-items: center;
            margin: auto;
            font-size: 30px;
        }

        h1 {
            letter-spacing: 5px;
            margin-bottom: 8px;
        }

        p {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
        }

        .tarjeta {
            background: #121827;
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #263247;
        }

        h2 {
            text-align: center;
            margin-top: 0;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #374151;
            border-radius: 9px;
            background: #090d16;
            color: white;
            margin-bottom: 18px;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #818cf8;
        }

        button {
            width: 100%;
            border: none;
            background: #6366f1;
            color: white;
            padding: 14px;
            border-radius: 9px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #4f46e5;
        }

        a {
            color: #a5b4fc;
            text-decoration: none;
        }

        .enlace, footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
        }

        .errores {
            background: #431c27;
            color: #fecaca;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        footer {
            color: #94a3b8;
        }
    </style>
</head>

<body>

@php
    $registro = request()->routeIs('register');
@endphp

<div class="contenedor">

    <div class="marca">
        <img src="{{ asset('branding/logo-nexoia.webp') }}"
     alt="NEXOIA"
     style="width:85px;height:85px;object-fit:contain;vertical-align:middle;border-radius:16px;">
        <h1>NEXOIA</h1>
        <p>Tu conexión con mejores decisiones.</p>
    </div>

    <div class="tarjeta">

        <h2>
            {{ $registro ? 'Crear cuenta' : 'Bienvenido' }}
        </h2>

        @if ($errors->any())
            <div class="errores">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST"
              action="{{ $registro ? route('register.store') : route('login.store') }}">

            @csrf

            @if ($registro)
                <label for="name">Nombre completo</label>

                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       placeholder="Tu nombre"
                       autocomplete="name" required>
            @endif

            <label for="email">Correo electrónico</label>

            <input type="email" id="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="correo@ejemplo.com"
                   autocomplete="email" required>

            <label for="password">Contraseña</label>

            <input type="password" id="password" name="password"
                   placeholder="Tu contraseña"
                   autocomplete="{{ $registro ? 'new-password' : 'current-password' }}"
                   required>

            @if ($registro)
                <label for="password_confirmation">
                    Confirmar contraseña
                </label>

                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       autocomplete="new-password" required>
            @endif

            <button type="submit">
                {{ $registro ? 'Crear cuenta' : 'Iniciar sesión' }}
            </button>

        </form>

        <div class="enlace">

            @if ($registro)
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}">Inicia sesión</a>
            @else
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}">Regístrate</a>
            @endif

        </div>

    </div>

    <footer>Powered by Google Gemini</footer>

</div>

</body>
</html>