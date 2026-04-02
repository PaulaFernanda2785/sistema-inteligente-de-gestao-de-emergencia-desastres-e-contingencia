<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abrigos | SIGEDC</title>
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
        .container { max-width: 1220px; margin: 28px auto; padding: 0 16px; }
        .topbar { display: flex; flex-wrap: wrap; gap: 10px; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .title { margin: 0; font-size: 24px; }
        .actions { display: flex; gap: 8px; }
        .btn { border: 0; border-radius: 8px; padding: 10px 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-soft { background: #eaf0fb; color: #1d4ed8; }
        .btn-danger { background: #fee2e2; color: #991b1b; padding: 4px 8px; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); margin-top: 12px; }
        .card-body { padding: 14px; }
        .grid { display: grid; gap: 10px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid-2 { display: grid; gap: 10px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .field label { display: block; margin-bottom: 6px; font-size: 13px; color: var(--muted); }
        .field input, .field select, .field textarea { width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--border); font-size: 14px; white-space: nowrap; vertical-align: top; }
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
        <h1 class="title">Abrigos Potenciais</h1>
        <div class="actions">
            <a class="btn btn-soft" href="{{ route('territory.territories.index') }}">Territorios</a>
            <a class="btn btn-soft" href="{{ route('risk.areas.index') }}">Areas de risco</a>
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
            <form method="get" action="{{ route('shelters.index') }}" class="grid">
                <div class="field">
                    <label for="territorial_unit_id">Unidade territorial</label>
                    <select id="territorial_unit_id" name="territorial_unit_id">
                        <option value="">Todas</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected((string) ($filters['territorial_unit_id'] ?? '') === (string) $unit->id)>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="shelter_type">Tipo</label>
                    <select id="shelter_type" name="shelter_type">
                        <option value="">Todos</option>
                        @foreach ($shelterTypes as $type)
                            <option value="{{ $type }}" @selected(($filters['shelter_type'] ?? '') === $type)>{{ $type }}</option>
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
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                </div>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <h2 style="margin:0 0 10px;">Novo abrigo</h2>
            <form method="post" action="{{ route('shelters.store') }}" class="grid-2">
                @csrf
                <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                <div class="field">
                    <label for="new_unit">Unidade territorial</label>
                    <select id="new_unit" name="territorial_unit_id" required>
                        <option value="">Selecione</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="new_name">Nome</label>
                    <input id="new_name" type="text" name="name" required maxlength="200">
                </div>
                <div class="field">
                    <label for="new_type">Tipo</label>
                    <select id="new_type" name="shelter_type" required>
                        <option value="">Selecione</option>
                        @foreach ($shelterTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="new_capacity">Capacidade maxima</label>
                    <input id="new_capacity" type="number" name="max_people_capacity" required min="0">
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="new_address">Endereco</label>
                    <textarea id="new_address" name="address" rows="2" required></textarea>
                </div>
                <div class="field">
                    <label for="new_manager">Responsavel</label>
                    <input id="new_manager" type="text" name="manager_name" maxlength="150">
                </div>
                <div class="field">
                    <label for="new_phone">Telefone</label>
                    <input id="new_phone" type="text" name="contact_phone" maxlength="30">
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="new_access">Acessibilidade</label>
                    <textarea id="new_access" name="accessibility_features" rows="2"></textarea>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="new_sanitary">Estrutura sanitaria</label>
                    <textarea id="new_sanitary" name="sanitary_structure_description" rows="2"></textarea>
                </div>
                <div class="field">
                    <label for="new_lat">Latitude</label>
                    <input id="new_lat" type="number" step="0.0000001" name="latitude" min="-90" max="90">
                </div>
                <div class="field">
                    <label for="new_lng">Longitude</label>
                    <input id="new_lng" type="number" step="0.0000001" name="longitude" min="-180" max="180">
                </div>
                <div class="field">
                    <label><input type="checkbox" name="kitchen_available" value="1"> Cozinha</label>
                </div>
                <div class="field">
                    <label><input type="checkbox" name="water_supply_available" value="1"> Agua</label>
                </div>
                <div class="field">
                    <label><input type="checkbox" name="energy_supply_available" value="1"> Energia</label>
                </div>
                <div class="field">
                    <label for="new_active">Status</label>
                    <select id="new_active" name="is_active" required>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <button class="btn btn-primary" type="submit">Salvar abrigo</button>
                </div>
            </form>
        </div>
    </section>

    @if ($editingShelter)
        <section class="card">
            <div class="card-body">
                <h2 style="margin:0 0 10px;">Editar abrigo #{{ $editingShelter->id }}</h2>
                <form method="post" action="{{ route('shelters.update', $editingShelter->id) }}" class="grid-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                    <div class="field">
                        <label for="edit_unit">Unidade territorial</label>
                        <select id="edit_unit" name="territorial_unit_id" required>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @selected((int) $editingShelter->territorial_unit_id === (int) $unit->id)>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="edit_name">Nome</label>
                        <input id="edit_name" type="text" name="name" required maxlength="200" value="{{ $editingShelter->name }}">
                    </div>
                    <div class="field">
                        <label for="edit_type">Tipo</label>
                        <select id="edit_type" name="shelter_type" required>
                            @foreach ($shelterTypes as $type)
                                <option value="{{ $type }}" @selected($editingShelter->shelter_type === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="edit_capacity">Capacidade maxima</label>
                        <input id="edit_capacity" type="number" name="max_people_capacity" required min="0" value="{{ $editingShelter->max_people_capacity }}">
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="edit_address">Endereco</label>
                        <textarea id="edit_address" name="address" rows="2" required>{{ $editingShelter->address }}</textarea>
                    </div>
                    <div class="field">
                        <label for="edit_manager">Responsavel</label>
                        <input id="edit_manager" type="text" name="manager_name" maxlength="150" value="{{ $editingShelter->manager_name }}">
                    </div>
                    <div class="field">
                        <label for="edit_phone">Telefone</label>
                        <input id="edit_phone" type="text" name="contact_phone" maxlength="30" value="{{ $editingShelter->contact_phone }}">
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="edit_access">Acessibilidade</label>
                        <textarea id="edit_access" name="accessibility_features" rows="2">{{ $editingShelter->accessibility_features }}</textarea>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="edit_sanitary">Estrutura sanitaria</label>
                        <textarea id="edit_sanitary" name="sanitary_structure_description" rows="2">{{ $editingShelter->sanitary_structure_description }}</textarea>
                    </div>
                    <div class="field">
                        <label for="edit_lat">Latitude</label>
                        <input id="edit_lat" type="number" step="0.0000001" name="latitude" min="-90" max="90" value="{{ $editingShelter->latitude }}">
                    </div>
                    <div class="field">
                        <label for="edit_lng">Longitude</label>
                        <input id="edit_lng" type="number" step="0.0000001" name="longitude" min="-180" max="180" value="{{ $editingShelter->longitude }}">
                    </div>
                    <div class="field">
                        <label><input type="checkbox" name="kitchen_available" value="1" @checked($editingShelter->kitchen_available)> Cozinha</label>
                    </div>
                    <div class="field">
                        <label><input type="checkbox" name="water_supply_available" value="1" @checked($editingShelter->water_supply_available)> Agua</label>
                    </div>
                    <div class="field">
                        <label><input type="checkbox" name="energy_supply_available" value="1" @checked($editingShelter->energy_supply_available)> Energia</label>
                    </div>
                    <div class="field">
                        <label for="edit_active">Status</label>
                        <select id="edit_active" name="is_active" required>
                            <option value="1" @selected($editingShelter->is_active)>Ativo</option>
                            <option value="0" @selected(!$editingShelter->is_active)>Inativo</option>
                        </select>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <button class="btn btn-primary" type="submit">Atualizar abrigo</button>
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
                    <th>Unidade</th>
                    <th>Tipo</th>
                    <th>Capacidade</th>
                    <th>Contato</th>
                    <th>Status</th>
                    <th>Acoes</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($shelters as $shelter)
                    <tr>
                        <td>{{ $shelter->id }}</td>
                        <td>{{ $shelter->name }}</td>
                        <td>{{ $shelter->territorialUnit?->name ?? '-' }}</td>
                        <td>{{ $shelter->shelter_type }}</td>
                        <td>{{ $shelter->max_people_capacity }}</td>
                        <td>{{ $shelter->contact_phone ?: '-' }}</td>
                        <td>
                            <span class="tag {{ $shelter->is_active ? 'ativo' : 'inativo' }}">
                                {{ $shelter->is_active ? 'ATIVO' : 'INATIVO' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('shelters.edit', $shelter->id) }}">Editar</a>
                            @if ($shelter->is_active)
                                |
                                <form class="inline-form" method="post" action="{{ route('shelters.deactivate', $shelter->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                                    <button type="submit" class="btn btn-danger">Inativar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Nenhum abrigo encontrado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination">
                <div>
                    Exibindo {{ $shelters->firstItem() ?? 0 }}-{{ $shelters->lastItem() ?? 0 }} de {{ $shelters->total() }}
                </div>
                <div>
                    @if ($shelters->onFirstPage())
                        <span>Anterior</span>
                    @else
                        <a href="{{ $shelters->previousPageUrl() }}">Anterior</a>
                    @endif
                    |
                    @if ($shelters->hasMorePages())
                        <a href="{{ $shelters->nextPageUrl() }}">Proxima</a>
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
