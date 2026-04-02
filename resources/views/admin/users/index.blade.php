<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios | SIGEDC</title>
    <style>
        :root {
            --bg: #f3f6fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #0f4c81;
            --border: #dbe3ee;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Tahoma, sans-serif; background: var(--bg); color: var(--text); }
        .container { max-width: 1120px; margin: 32px auto; padding: 0 16px; }
        .header { display: flex; gap: 12px; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .title { margin: 0; font-size: 24px; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); }
        .card-body { padding: 16px; }
        .filters { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
        .filters input, .filters select { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; }
        .filters button, .logout-btn { border: 0; border-radius: 8px; padding: 10px 14px; font-weight: 600; cursor: pointer; }
        .filters button { background: var(--primary); color: #fff; }
        .logout-btn { background: #eef2ff; color: #1e3a8a; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--border); font-size: 14px; white-space: nowrap; }
        th { font-size: 12px; text-transform: uppercase; color: var(--muted); letter-spacing: .04em; }
        .tag { border-radius: 999px; padding: 4px 8px; font-size: 12px; font-weight: 600; }
        .tag.ativo { background: #dcfce7; color: #166534; }
        .tag.inativo { background: #fee2e2; color: #991b1b; }
        .pagination { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; color: var(--muted); font-size: 14px; }
        .pagination a { color: #1d4ed8; text-decoration: none; }
        .empty { padding: 16px 8px; color: var(--muted); }
        @media (max-width: 900px) {
            .filters { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 560px) {
            .filters { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<main class="container">
    <section class="header">
        <h1 class="title">Usuarios do Sistema</h1>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Sair</button>
        </form>
    </section>

    <section class="card">
        <div class="card-body">
            <form method="get" action="{{ route('admin.users.index') }}" class="filters">
                <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" placeholder="Nome">
                <input type="email" name="email" value="{{ $filters['email'] ?? '' }}" placeholder="E-mail">
                <select name="status">
                    <option value="">Todos os status</option>
                    <option value="ATIVO" @selected(($filters['status'] ?? '') === 'ATIVO')>Ativo</option>
                    <option value="INATIVO" @selected(($filters['status'] ?? '') === 'INATIVO')>Inativo</option>
                </select>
                <button type="submit">Filtrar</button>
            </form>
        </div>
    </section>

    <section class="card" style="margin-top: 14px;">
        <div class="card-body table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Organizacao</th>
                    <th>Status</th>
                    <th>Perfis</th>
                    <th>Ultimo acesso</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->organization?->name ?? '-' }}</td>
                        <td>
                            <span class="tag {{ strtolower($user->status) }}">
                                {{ $user->status }}
                            </span>
                        </td>
                        <td>{{ $user->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                        <td>{{ optional($user->last_login_at)->format('d/m/Y H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">Nenhum usuario encontrado com os filtros informados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination">
                <div>
                    Exibindo {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} de {{ $users->total() }} usuarios
                </div>
                <div>
                    @if ($users->onFirstPage())
                        <span>Anterior</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}">Anterior</a>
                    @endif
                    |
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}">Proxima</a>
                    @else
                        <span>Proxima</span>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>
</body>
</html>
