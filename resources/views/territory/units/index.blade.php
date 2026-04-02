@extends('layouts.app')

@section('title', 'Unidades territoriais')
@section('page_title', 'Base Territorial - Unidades territoriais')

@section('page_actions')
    <a class="btn btn-soft" href="{{ route('territory.territories.index') }}">Territorios</a>
@endsection

@section('content')
    @if (session('success'))
        <div class="feedback-ok">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="feedback-err">{{ $errors->first() }}</div>
    @endif

    <section class="card">
        <div class="card-body">
            <form method="get" action="{{ route('territory.units.index') }}" class="grid">
                <div class="field">
                    <label for="territory_id">Territorio</label>
                    <select id="territory_id" name="territory_id">
                        <option value="">Todos</option>
                        @foreach ($territories as $territory)
                            <option value="{{ $territory->id }}" @selected((string) ($filters['territory_id'] ?? '') === (string) $territory->id)>
                                {{ $territory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="name">Nome</label>
                    <input id="name" type="text" name="name" value="{{ $filters['name'] ?? '' }}" maxlength="200">
                </div>
                <div class="field">
                    <label for="unit_type">Tipo</label>
                    <input id="unit_type" type="text" name="unit_type" value="{{ $filters['unit_type'] ?? '' }}" maxlength="100">
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
            <h2 style="margin:0 0 10px;">Nova unidade territorial</h2>
            <form method="post" action="{{ route('territory.units.store') }}" class="grid-2">
                @csrf
                <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                <div class="field">
                    <label for="new_territory_id">Territorio</label>
                    <select id="new_territory_id" name="territory_id" required>
                        <option value="">Selecione</option>
                        @foreach ($territories as $territory)
                            <option value="{{ $territory->id }}" @selected((string) ($filters['territory_id'] ?? '') === (string) $territory->id)>
                                {{ $territory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="new_parent_unit_id">Unidade pai</label>
                    <input id="new_parent_unit_id" type="number" name="parent_unit_id" min="1">
                </div>
                <div class="field">
                    <label for="new_name">Nome</label>
                    <input id="new_name" type="text" name="name" required maxlength="200">
                </div>
                <div class="field">
                    <label for="new_type">Tipo</label>
                    <input id="new_type" type="text" name="unit_type" required maxlength="100">
                </div>
                <div class="field">
                    <label for="new_code">Codigo interno</label>
                    <input id="new_code" type="text" name="code" maxlength="50">
                </div>
                <div class="field">
                    <label for="new_pop">Populacao estimada</label>
                    <input id="new_pop" type="number" name="population_estimate" min="0">
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <button class="btn btn-primary" type="submit">Salvar unidade</button>
                </div>
            </form>
        </div>
    </section>

    @if ($editingUnit)
        <section class="card">
            <div class="card-body">
                <h2 style="margin:0 0 10px;">Editar unidade #{{ $editingUnit->id }}</h2>
                <form method="post" action="{{ route('territory.units.update', $editingUnit->id) }}" class="grid-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                    <div class="field">
                        <label for="edit_territory_id">Territorio</label>
                        <select id="edit_territory_id" name="territory_id" required>
                            <option value="">Selecione</option>
                            @foreach ($territories as $territory)
                                <option value="{{ $territory->id }}" @selected((int) $editingUnit->territory_id === (int) $territory->id)>
                                    {{ $territory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="edit_parent_unit_id">Unidade pai</label>
                        <input id="edit_parent_unit_id" type="number" name="parent_unit_id" min="1" value="{{ $editingUnit->parent_unit_id }}">
                    </div>
                    <div class="field">
                        <label for="edit_name">Nome</label>
                        <input id="edit_name" type="text" name="name" required maxlength="200" value="{{ $editingUnit->name }}">
                    </div>
                    <div class="field">
                        <label for="edit_type">Tipo</label>
                        <input id="edit_type" type="text" name="unit_type" required maxlength="100" value="{{ $editingUnit->unit_type }}">
                    </div>
                    <div class="field">
                        <label for="edit_code">Codigo interno</label>
                        <input id="edit_code" type="text" name="code" maxlength="50" value="{{ $editingUnit->code }}">
                    </div>
                    <div class="field">
                        <label for="edit_pop">Populacao estimada</label>
                        <input id="edit_pop" type="number" name="population_estimate" min="0" value="{{ $editingUnit->population_estimate }}">
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <button class="btn btn-primary" type="submit">Atualizar unidade</button>
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
                    <th>Territorio</th>
                    <th>Tipo</th>
                    <th>Pai</th>
                    <th>Codigo</th>
                    <th>Populacao</th>
                    <th>Acoes</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($units as $unit)
                    <tr>
                        <td>{{ $unit->id }}</td>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->territory?->name ?? '-' }}</td>
                        <td>{{ $unit->unit_type }}</td>
                        <td>{{ $unit->parent?->name ?? '-' }}</td>
                        <td>{{ $unit->code ?: '-' }}</td>
                        <td>{{ $unit->population_estimate ?? '-' }}</td>
                        <td>
                            <a href="{{ route('territory.units.edit', $unit->id) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Nenhuma unidade territorial encontrada.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination">
                <div>
                    Exibindo {{ $units->firstItem() ?? 0 }}-{{ $units->lastItem() ?? 0 }} de {{ $units->total() }}
                </div>
                <div>
                    @if ($units->onFirstPage())
                        <span>Anterior</span>
                    @else
                        <a href="{{ $units->previousPageUrl() }}">Anterior</a>
                    @endif
                    |
                    @if ($units->hasMorePages())
                        <a href="{{ $units->nextPageUrl() }}">Proxima</a>
                    @else
                        <span>Proxima</span>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
