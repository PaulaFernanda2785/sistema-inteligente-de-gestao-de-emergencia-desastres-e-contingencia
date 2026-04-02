@extends('layouts.app')

@section('title', 'Territorios')
@section('page_title', 'Base Territorial - Territorios')

@section('page_actions')
    <a class="btn btn-soft" href="{{ route('territory.units.index') }}">Unidades territoriais</a>
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
@endsection
