<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIGEDC</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f2f5f8; }
        .container { max-width: 420px; margin: 8vh auto; background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
        .title { margin: 0 0 16px; color: #1f2937; }
        .group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-size: 14px; color: #374151; }
        input[type="email"], input[type="password"] { width: 100%; box-sizing: border-box; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .remember { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; font-size: 14px; color: #374151; }
        button { width: 100%; border: 0; border-radius: 6px; background: #0d6efd; color: #fff; padding: 11px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; }
        button:disabled { opacity: .72; cursor: not-allowed; }
        .spinner { width: 14px; height: 14px; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 999px; animation: spin .7s linear infinite; display: none; }
        .is-loading .spinner { display: inline-block; }
        .error { margin-bottom: 14px; color: #b91c1c; font-size: 14px; }
        .warning { margin-bottom: 14px; color: #92400e; font-size: 14px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
<main class="container">
    <h1 class="title">SIGEDC - Acesso</h1>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    @if (session('warning'))
        <div class="warning">{{ session('warning') }}</div>
    @endif

    <form method="post" action="{{ route('login.store') }}" data-single-submit>
        @csrf
        <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

        <div class="group">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="150" autocomplete="email">
        </div>

        <div class="group">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required minlength="8" maxlength="255" autocomplete="current-password">
        </div>

        <label class="remember" for="remember">
            <input id="remember" type="checkbox" name="remember" value="1">
            Manter sessão
        </label>

        <button type="submit" data-submit-button>
            <span class="spinner" aria-hidden="true"></span>
            <span data-submit-label>Entrar</span>
        </button>
    </form>
</main>

<script>
    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-single-submit')) {
            return;
        }

        if (form.dataset.submitting === '1') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = '1';

        var button = form.querySelector('[data-submit-button]');
        var label = form.querySelector('[data-submit-label]');
        if (button instanceof HTMLButtonElement) {
            button.disabled = true;
            button.classList.add('is-loading');
        }
        if (label) {
            label.textContent = 'Processando...';
        }
    });
</script>
</body>
</html>
