@extends('layouts.app')

@section('title', 'Bairros')
@section('page_title', 'Base Territorial - Bairros')

@section('page_actions')
    <a class="btn btn-soft" href="{{ route('territory.territories.index') }}">Territorios</a>
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
            <form method="get" action="{{ route('territory.bairros.index') }}" class="grid">
                <div class="field">
                    <label for="municipio_id">Municipio</label>
                    <select id="municipio_id" name="municipio_id">
                        <option value="">Todos</option>
                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio->id }}" @selected((string) ($filters['municipio_id'] ?? '') === (string) $municipio->id)>
                                {{ $municipio->nome }} ({{ $municipio->uf }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="nome">Nome</label>
                    <input id="nome" type="text" name="nome" value="{{ $filters['nome'] ?? '' }}" maxlength="150">
                </div>
                <div class="field">
                    <label for="codigo_ibge">Codigo IBGE</label>
                    <input id="codigo_ibge" type="text" name="codigo_ibge" value="{{ $filters['codigo_ibge'] ?? '' }}" maxlength="30">
                </div>
                <div class="field">
                    <label for="ativo">Status</label>
                    <select id="ativo" name="ativo">
                        <option value="">Todos</option>
                        <option value="1" @selected(($filters['ativo'] ?? '') === '1')>Ativo</option>
                        <option value="0" @selected(($filters['ativo'] ?? '') === '0')>Inativo</option>
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
            <h2 style="margin:0 0 10px;">Novo bairro</h2>
            <form method="post" action="{{ route('territory.bairros.store') }}" class="grid-2">
                @csrf
                <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                <div class="field">
                    <label for="new_municipio_id">Municipio</label>
                    <select id="new_municipio_id" name="municipio_id" required>
                        <option value="">Selecione</option>
                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio->id }}">{{ $municipio->nome }} ({{ $municipio->uf }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="new_nome">Nome</label>
                    <input id="new_nome" type="text" name="nome" required maxlength="150">
                </div>
                <div class="field">
                    <label for="new_codigo_ibge">Codigo IBGE</label>
                    <input id="new_codigo_ibge" type="text" name="codigo_ibge" maxlength="30">
                </div>
                <div class="field">
                    <label for="new_ativo">Status</label>
                    <select id="new_ativo" name="ativo" required>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="new_geojson">GeoJSON de referencia</label>
                    <textarea id="new_geojson" name="geojson_referencia" rows="3"></textarea>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <button class="btn btn-primary" type="submit">Salvar bairro</button>
                </div>
            </form>
        </div>
    </section>

    @if ($editingBairro)
        <section class="card">
            <div class="card-body">
                <h2 style="margin:0 0 10px;">Editar bairro #{{ $editingBairro->id }}</h2>
                <form method="post" action="{{ route('territory.bairros.update', $editingBairro->id) }}" class="grid-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

                    <div class="field">
                        <label for="edit_municipio_id">Municipio</label>
                        <select id="edit_municipio_id" name="municipio_id" required>
                            @foreach ($municipios as $municipio)
                                <option value="{{ $municipio->id }}" @selected((int) $editingBairro->municipio_id === (int) $municipio->id)>
                                    {{ $municipio->nome }} ({{ $municipio->uf }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="edit_nome">Nome</label>
                        <input id="edit_nome" type="text" name="nome" required maxlength="150" value="{{ $editingBairro->nome }}">
                    </div>
                    <div class="field">
                        <label for="edit_codigo_ibge">Codigo IBGE</label>
                        <input id="edit_codigo_ibge" type="text" name="codigo_ibge" maxlength="30" value="{{ $editingBairro->codigo_ibge }}">
                    </div>
                    <div class="field">
                        <label for="edit_ativo">Status</label>
                        <select id="edit_ativo" name="ativo" required>
                            <option value="1" @selected($editingBairro->ativo)>Ativo</option>
                            <option value="0" @selected(!$editingBairro->ativo)>Inativo</option>
                        </select>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="edit_geojson">GeoJSON de referencia</label>
                        <textarea id="edit_geojson" name="geojson_referencia" rows="3">{{ $editingBairro->geojson_referencia }}</textarea>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <button class="btn btn-primary" type="submit">Atualizar bairro</button>
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
                    <th>Municipio</th>
                    <th>UF</th>
                    <th>Codigo IBGE</th>
                    <th>Status</th>
                    <th>Acoes</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($bairros as $bairro)
                    <tr>
                        <td>{{ $bairro->id }}</td>
                        <td>{{ $bairro->nome }}</td>
                        <td>{{ $bairro->municipio?->nome ?? '-' }}</td>
                        <td>{{ $bairro->municipio?->uf ?? '-' }}</td>
                        <td>{{ $bairro->codigo_ibge ?: '-' }}</td>
                        <td>
                            <span class="tag {{ $bairro->ativo ? 'ativo' : 'inativo' }}">
                                {{ $bairro->ativo ? 'ATIVO' : 'INATIVO' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('territory.bairros.edit', $bairro->id) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhum bairro encontrado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination">
                <div>
                    Exibindo {{ $bairros->firstItem() ?? 0 }}-{{ $bairros->lastItem() ?? 0 }} de {{ $bairros->total() }}
                </div>
                <div>
                    @if ($bairros->onFirstPage())
                        <span>Anterior</span>
                    @else
                        <a href="{{ $bairros->previousPageUrl() }}">Anterior</a>
                    @endif
                    |
                    @if ($bairros->hasMorePages())
                        <a href="{{ $bairros->nextPageUrl() }}">Proxima</a>
                    @else
                        <span>Proxima</span>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
