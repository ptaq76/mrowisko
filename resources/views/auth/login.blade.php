<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts._meta')
    <title>Mrowisko</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --green: #6EBF58; --black: #1a1a1a;
            --gray-1: #f4f5f7; --gray-2: #e2e5e9; --gray-3: #9aa3ad;
            --white: #ffffff; --radius: 6px;
        }
        html, body {
            height: 100%; font-family: 'Barlow', sans-serif;
            background: var(--gray-1); display: flex;
            align-items: center; justify-content: center;
        }
        .login-wrap { width: 100%; max-width: 400px; padding: 20px; }
        .login-card { background: var(--white); border-radius: var(--radius); box-shadow: 0 4px 24px rgba(0,0,0,.10); overflow: hidden; }
        .login-header { background: var(--green); padding: 28px 32px 24px; text-align: center; }
        .login-header img { height: 48px; width: auto; }
        .login-header .tagline { margin-top: 10px; font-family: 'Barlow Condensed', sans-serif; font-size: 13px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: rgba(0,0,0,.55); }
        .login-body { padding: 32px; }
        .login-body h2 { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: .04em; color: var(--black); margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--black); }
        .input-wrap { position: relative; }
        .input-wrap .icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--gray-3); font-size: 14px; }
        .form-control { width: 100%; padding: 10px 12px 10px 36px; border: 1px solid var(--gray-2); border-radius: var(--radius); font-family: 'Barlow', sans-serif; font-size: 14px; color: var(--black); background: var(--white); outline: none; transition: border-color .15s, box-shadow .15s; }
        .form-control:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(110,191,88,.18); }
        .form-control.is-invalid { border-color: #e74c3c; }
        .invalid-feedback { display: block; margin-top: 5px; font-size: 12px; color: #e74c3c; }
        .btn-login { width: 100%; padding: 11px; background: var(--green); color: var(--black); border: none; border-radius: var(--radius); font-family: 'Barlow Condensed', sans-serif; font-size: 16px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; cursor: pointer; transition: filter .15s, transform .1s; margin-top: 6px; }
        .btn-login:hover { filter: brightness(.92); }
        .btn-login:active { transform: scale(.98); }
        .login-footer { padding: 14px 32px; border-top: 1px solid var(--gray-2); text-align: center; font-size: 12px; color: var(--gray-3); }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <div class="tagline">System zarządzania</div>
        </div>
        <div class="login-body">
            <h2>Logowanie</h2>
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="login">Login</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user icon"></i>
                        <input type="text" id="login" name="login"
                            class="form-control @error('login') is-invalid @enderror"
                            value="{{ old('login') }}" autocomplete="username" autofocus>
                    </div>
                    @error('login')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Hasło</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock icon"></i>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="current-password">
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Zaloguj się
                </button>
            </form>
        </div>
        <div class="login-footer">
            Skontaktuj się z administratorem w celu uzyskania dostępu.
        </div>
    </div>
</div>
</body>
</html>