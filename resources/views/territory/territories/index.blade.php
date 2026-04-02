<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Territorios | SIGEDC</title>
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
        .container { max-width: 1180px; margin: 28px auto; padding: 0 16px; }
        .topbar { display: flex; flex-wrap: wrap; gap: 10px; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .title { margin: 0; font-size: 24px; }
        .actions { display: flex; gap: 8px; }
        .btn { border: 0; border-radius: 8px; padding: 10px 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-soft { background: #eaf0fb; color: #1d4ed8; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); margin-top: 12px; }
        .card-body { padding: 14px; }
        .grid { display: grid; gap: 10px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid-2 { display: grid; gap: 10px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .field label { display: block; margin-bottom: 6px; font-size: 13px; color: var(--muted); }
        .field input, .field select, .field textarea { width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--border); font-size: 14px; white-space: nowrap; }
        th { font-size: 12px; text-transform: uppercase; color: var(--muted); letter-spacing: .04em; }
        .feedback-ok { margin-top: 8px; color: #166534; }
        .feedback-err { margin-top: 8px; color: #991b1b; }
        .table-wrap { overflow-x: auto; }
        .pagination { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; color: var(--muted); font-size: 14px; }
        .pagination a { color: #1d4ed8; text-decoration: none; }
        @media (max-width: 920px) {
            .grid, .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<main class="container">
    <section class="topbar">
        <h1 class="title">Base Territorial - Territorios</h1>
        <div class="actions">
            <a class="btn btn-soft" href="{{ route('territory.units.index') }}">Unidades territoriais</a>
            <a class="btn btn-soft" href="{{ route('admin.users.index') }}">Usuarios</a>
        </div>
    </section>

    @if (session('success'))
        <div class="feedback-ok">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="feedback-err">{{ $errors->first() }}</div>
    @endif

    <section class="card">
        <div class="card-body">
            <form method="get" action="{{ route('territory.territories.index') }}" class="grid">
                <div class="field">
                    <label for="name">Nome</label>
                    <input id="name" type="text" name="name" value="{{ $filters['name'] ?? '' }}" maxlength="200">
                </div>
                <div class="field">
                    <label for="territory_type">Tipo territorial</label>
                    <input id="territory_type" type="text" name="territory_type" value="{{ $filters['territory_type'] ?? '' }}" maxlength="100">
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                </div>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <h2 style="margin:0 0 10px;">Novo territorio</h2>
            <form method="post" action="{{ route('territory.territories.store') }}" class="grid-2">
                @csrf
                <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                <div class="field">
                    <label for="new_name">Nome</label>
                    <input id="new_name" type="text" name="name" required maxlength="200">
                </div>
                <div class="field">
                    <label for="new_type">Tipo territorial</label>
                    <input id="new_type" type="text" name="territory_type" required maxlength="100">
                </div>
                <div class="field">
                    <label for="new_state">UF</label>
                    <input id="new_state" type="text" name="state_code" required maxlength="2">
                </div>
                <div class="field">
                    <label for="new_ibge">Codigo IBGE</label>
                    <input id="new_ibge" type="text" name="ibge_code" maxlength="20">
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="new_desc">Descricao</label>
                    <textarea id="new_desc" name="description" rows="3"></textarea>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <button class="btn btn-primary" type="submit">Salvar territorio</button>
                </div>
            </form>
        </div>
    </section>

    @if ($editingTerritory)
        <section class="card">
            <div class="card-body">
                <h2 style="margin:0 0 10px;">Editar territorio #{{ $editingTerritory->id }}</h2>
                <form method="post" action="{{ route('territory.territories.update', $editingTerritory->id) }}" class="grid-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                    <div class="field">
                        <label for="edit_name">Nome</label>
                        <input id="edit_name" type="text" name="name" required maxlength="200" value="{{ $editingTerritory->name }}">
                    </div>
                    <div class="field">
                        <label for="edit_type">Tipo territorial</label>
                        <input id="edit_type" type="text" name="territory_type" required maxlength="100" value="{{ $editingTerritory->territory_type }}">
                    </div>
                    <div class="field">
                        <label for="edit_state">UF</label>
                        <input id="edit_state" type="text" name="state_code" required maxlength="2" value="{{ $editingTerritory->state_code }}">
                    </div>
                    <div class="field">
                        <label for="edit_ibge">Codigo IBGE</label>
                        <input id="edit_ibge" type="text" name="ibge_code" maxlength="20" value="{{ $editingTerritory->ibge_code }}">
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="edit_desc">Descricao</label>
                        <textarea id="edit_desc" name="description" rows="3">{{ $editingTerritory->description }}</textarea>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <button class="btn btn-primary" type="submit">Atualizar territorio</button>
                    </div>
                </form>
            </div>
        </section>
    @endif

    <section class="card">
        <div class="card-body table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>UF</th>
                    <th>IBGE</th>
                    <th>Unidades</th>
                    <th>Acoes</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($territories as $territory)
                    <tr>
                        <td>{{ $territory->id }}</td>
                        <td>{{ $territory->name }}</td>
                        <td>{{ $territory->territory_type }}</td>
                        <td>{{ $territory->state_code }}</td>
                        <td>{{ $territory->ibge_code ?: '-' }}</td>
                        <td>{{ $territory->units_count }}</td>
                        <td>
                            <a href="{{ route('territory.territories.edit', $territory->id) }}">Editar</a>
                            |
                            <a href="{{ route('territory.units.index', ['territory_id' => $territory->id]) }}">Unidades</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhum territorio encontrado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination">
                <div>
                    Exibindo {{ $territories->firstItem() ?? 0 }}-{{ $territories->lastItem() ?? 0 }} de {{ $territories->total() }}
                </div>
                <div>
                    @if ($territories->onFirstPage())
                        <span>Anterior</span>
                    @else
                        <a href="{{ $territories->previousPageUrl() }}">Anterior</a>
                    @endif
                    |
                    @if ($territories->hasMorePages())
                        <a href="{{ $territories->nextPageUrl() }}">Proxima</a>
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
