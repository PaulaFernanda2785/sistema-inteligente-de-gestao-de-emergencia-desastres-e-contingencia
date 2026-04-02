<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Areas de risco | SIGEDC</title>
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
        .btn-danger { background: #fee2e2; color: #991b1b; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); margin-top: 12px; }
        .card-body { padding: 14px; }
        .grid { display: grid; gap: 10px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid-2 { display: grid; gap: 10px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .field label { display: block; margin-bottom: 6px; font-size: 13px; color: var(--muted); }
        .field input, .field select, .field textarea { width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--border); font-size: 14px; white-space: nowrap; }
        th { font-size: 12px; text-transform: uppercase; color: var(--muted); letter-spacing: .04em; }
        .tag { border-radius: 999px; padding: 4px 8px; font-size: 12px; font-weight: 600; }
        .tag.ativo { background: #dcfce7; color: #166534; }
        .tag.inativo { background: #fee2e2; color: #991b1b; }
        .feedback-ok { margin-top: 8px; color: #166534; }
        .feedback-err { margin-top: 8px; color: #991b1b; }
        .table-wrap { overflow-x: auto; }
        .inline-form { display: inline; }
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
        <h1 class="title">Base Territorial - Areas de risco</h1>
        <div class="actions">
            <a class="btn btn-soft" href="{{ route('territory.territories.index') }}">Territorios</a>
            <a class="btn btn-soft" href="{{ route('territory.units.index') }}">Unidades</a>
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
            <form method="get" action="{{ route('risk.areas.index') }}" class="grid">
                <div class="field">
                    <label for="territorial_unit_id">Unidade territorial</label>
                    <select id="territorial_unit_id" name="territorial_unit_id">
                        <option value="">Todas</option>
                        @foreach ($territorialUnits as $unit)
                            <option value="{{ $unit->id }}" @selected((string) ($filters['territorial_unit_id'] ?? '') === (string) $unit->id)>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="risk_type">Tipo de risco</label>
                    <select id="risk_type" name="risk_type">
                        <option value="">Todos</option>
                        @foreach ($riskTypes as $type)
                            <option value="{{ $type }}" @selected(($filters['risk_type'] ?? '') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="priority_level">Prioridade</label>
                    <select id="priority_level" name="priority_level">
                        <option value="">Todas</option>
                        @foreach ($priorityLevels as $priority)
                            <option value="{{ $priority }}" @selected(($filters['priority_level'] ?? '') === $priority)>{{ $priority }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active">
                        <option value="">Todos</option>
                        <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Ativo</option>
                        <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inativo</option>
                    </select>
                </div>
                <div class="field">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                </div>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <h2 style="margin:0 0 10px;">Nova area de risco</h2>
            <form method="post" action="{{ route('risk.areas.store') }}" class="grid-2">
                @csrf
                <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                <div class="field">
                    <label for="new_unit">Unidade territorial</label>
                    <select id="new_unit" name="territorial_unit_id" required>
                        <option value="">Selecione</option>
                        @foreach ($territorialUnits as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="new_name">Nome</label>
                    <input id="new_name" type="text" name="name" required maxlength="200">
                </div>
                <div class="field">
                    <label for="new_risk_type">Tipo de risco</label>
                    <select id="new_risk_type" name="risk_type" required>
                        <option value="">Selecione</option>
                        @foreach ($riskTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="new_priority">Prioridade</label>
                    <select id="new_priority" name="priority_level" required>
                        <option value="">Selecione</option>
                        @foreach ($priorityLevels as $priority)
                            <option value="{{ $priority }}">{{ $priority }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="new_pop">Populacao exposta</label>
                    <input id="new_pop" type="number" name="exposed_population_estimate" min="0">
                </div>
                <div class="field">
                    <label for="new_active">Status</label>
                    <select id="new_active" name="is_active" required>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="new_desc">Descricao</label>
                    <textarea id="new_desc" name="description" rows="3"></textarea>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="new_notes">Observacoes de monitoramento</label>
                    <textarea id="new_notes" name="monitoring_notes" rows="3"></textarea>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <button class="btn btn-primary" type="submit">Salvar area</button>
                </div>
            </form>
        </div>
    </section>

    @if ($editingArea)
        <section class="card">
            <div class="card-body">
                <h2 style="margin:0 0 10px;">Editar area #{{ $editingArea->id }}</h2>
                <form method="post" action="{{ route('risk.areas.update', $editingArea->id) }}" class="grid-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                    <div class="field">
                        <label for="edit_unit">Unidade territorial</label>
                        <select id="edit_unit" name="territorial_unit_id" required>
                            <option value="">Selecione</option>
                            @foreach ($territorialUnits as $unit)
                                <option value="{{ $unit->id }}" @selected((int) $editingArea->territorial_unit_id === (int) $unit->id)>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="edit_name">Nome</label>
                        <input id="edit_name" type="text" name="name" required maxlength="200" value="{{ $editingArea->name }}">
                    </div>
                    <div class="field">
                        <label for="edit_risk_type">Tipo de risco</label>
                        <select id="edit_risk_type" name="risk_type" required>
                            @foreach ($riskTypes as $type)
                                <option value="{{ $type }}" @selected($editingArea->risk_type === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="edit_priority">Prioridade</label>
                        <select id="edit_priority" name="priority_level" required>
                            @foreach ($priorityLevels as $priority)
                                <option value="{{ $priority }}" @selected($editingArea->priority_level === $priority)>{{ $priority }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="edit_pop">Populacao exposta</label>
                        <input id="edit_pop" type="number" name="exposed_population_estimate" min="0" value="{{ $editingArea->exposed_population_estimate }}">
                    </div>
                    <div class="field">
                        <label for="edit_active">Status</label>
                        <select id="edit_active" name="is_active" required>
                            <option value="1" @selected($editingArea->is_active)>Ativo</option>
                            <option value="0" @selected(!$editingArea->is_active)>Inativo</option>
                        </select>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="edit_desc">Descricao</label>
                        <textarea id="edit_desc" name="description" rows="3">{{ $editingArea->description }}</textarea>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="edit_notes">Observacoes de monitoramento</label>
                        <textarea id="edit_notes" name="monitoring_notes" rows="3">{{ $editingArea->monitoring_notes }}</textarea>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <button class="btn btn-primary" type="submit">Atualizar area</button>
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
                    <th>Unidade territorial</th>
                    <th>Tipo de risco</th>
                    <th>Prioridade</th>
                    <th>Populacao exposta</th>
                    <th>Status</th>
                    <th>Acoes</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($areas as $area)
                    <tr>
                        <td>{{ $area->id }}</td>
                        <td>{{ $area->name }}</td>
                        <td>{{ $area->territorialUnit?->name ?? '-' }}</td>
                        <td>{{ $area->risk_type }}</td>
                        <td>{{ $area->priority_level }}</td>
                        <td>{{ $area->exposed_population_estimate ?? '-' }}</td>
                        <td>
                            <span class="tag {{ $area->is_active ? 'ativo' : 'inativo' }}">
                                {{ $area->is_active ? 'ATIVO' : 'INATIVO' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('risk.areas.edit', $area->id) }}">Editar</a>
                            @if ($area->is_active)
                                |
                                <form class="inline-form" method="post" action="{{ route('risk.areas.deactivate', $area->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                                    <button type="submit" class="btn btn-danger" style="padding:4px 8px;">Inativar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Nenhuma area de risco encontrada.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination">
                <div>
                    Exibindo {{ $areas->firstItem() ?? 0 }}-{{ $areas->lastItem() ?? 0 }} de {{ $areas->total() }}
                </div>
                <div>
                    @if ($areas->onFirstPage())
                        <span>Anterior</span>
                    @else
                        <a href="{{ $areas->previousPageUrl() }}">Anterior</a>
                    @endif
                    |
                    @if ($areas->hasMorePages())
                        <a href="{{ $areas->nextPageUrl() }}">Proxima</a>
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
