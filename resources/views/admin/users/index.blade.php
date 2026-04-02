@extends('layouts.app')

@section('title', 'Usuarios')
@section('page_title', 'Usuarios do Sistema')

@section('content')
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
@endsection
