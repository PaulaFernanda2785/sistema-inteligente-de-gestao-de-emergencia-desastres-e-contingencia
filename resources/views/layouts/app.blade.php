<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIGEDC') | SIGEDC</title>
    <style>
        :root {
            --bg: #f3f6fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #0f4c81;
            --border: #dbe3ee;
            --menu-bg: #0b3457;
            --menu-active: #154f80;
            --menu-text: #dbeafe;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .app-header {
            background: var(--menu-bg);
            border-bottom: 1px solid #0a2a45;
            padding: 10px 0;
        }

        .app-shell {
            max-width: 1220px;
            margin: 0 auto;
            padding: 0 16px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 12px;
            align-items: center;
        }

        .brand {
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .module-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .module-link {
            color: var(--menu-text);
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
        }

        .module-link:hover {
            background: var(--menu-active);
        }

        .module-link.active {
            background: var(--menu-active);
            color: #ffffff;
            font-weight: 600;
        }

        .logout-btn {
            border: 0;
            border-radius: 8px;
            padding: 9px 14px;
            font-weight: 600;
            cursor: pointer;
            background: #eaf0fb;
            color: #1e3a8a;
        }

        .container {
            max-width: 1220px;
            margin: 28px auto;
            padding: 0 16px;
        }

        .page-header {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .title {
            margin: 0;
            font-size: 24px;
        }

        .page-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn {
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-soft {
            background: #eaf0fb;
            color: #1d4ed8;
        }

        .btn-danger {
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
            margin-top: 12px;
        }

        .card-body {
            padding: 14px;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .grid-2 {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            color: var(--muted);
        }

        .field input,
        .field select,
        .field textarea,
        .filters input,
        .filters select {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px;
        }

        .filters button {
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 600;
            cursor: pointer;
            background: var(--primary);
            color: #fff;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            white-space: nowrap;
            vertical-align: top;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            color: var(--muted);
            letter-spacing: .04em;
        }

        .tag {
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .tag.ativo {
            background: #dcfce7;
            color: #166534;
        }

        .tag.inativo {
            background: #fee2e2;
            color: #991b1b;
        }

        .feedback-ok {
            margin-top: 8px;
            color: #166534;
        }

        .feedback-err {
            margin-top: 8px;
            color: #991b1b;
        }

        .inline-form {
            display: inline;
        }

        .empty {
            padding: 16px 8px;
            color: var(--muted);
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            color: var(--muted);
            font-size: 14px;
        }

        .pagination a {
            color: #1d4ed8;
            text-decoration: none;
        }

        @media (max-width: 920px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .page-header {
                align-items: flex-start;
            }

            .filters,
            .grid,
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
@php
    /** @var \App\Modules\Admin\Models\User|null $authUser */
    $authUser = auth()->user();
    $canUsers = $authUser?->hasPermission('users.view') ?? false;
    $canTerritories = ($authUser?->hasPermission('territories.view') ?? false) || ($authUser?->hasPermission('territorial_units.view') ?? false);
    $canRiskAreas = $authUser?->hasPermission('risk_areas.view') ?? false;
    $canShelters = $authUser?->hasPermission('shelters.view') ?? false;
    $territoryRoute = ($authUser?->hasPermission('territories.view') ?? false)
        ? route('territory.territories.index')
        : route('territory.units.index');
@endphp

<header class="app-header">
    <div class="app-shell">
        <div class="brand">SIGEDC</div>

        <nav class="module-menu" aria-label="Menu principal">
            @if ($canUsers)
                <a class="module-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
            @endif
            @if ($canTerritories)
                <a class="module-link {{ request()->routeIs('territory.*') ? 'active' : '' }}" href="{{ $territoryRoute }}">Territory</a>
            @endif
            @if ($canRiskAreas)
                <a class="module-link {{ request()->routeIs('risk.*') ? 'active' : '' }}" href="{{ route('risk.areas.index') }}">Risk</a>
            @endif
            @if ($canShelters)
                <a class="module-link {{ request()->routeIs('shelters.*') ? 'active' : '' }}" href="{{ route('shelters.index') }}">Shelters</a>
            @endif
        </nav>

        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Sair</button>
        </form>
    </div>
</header>

<main class="container">
    <section class="page-header">
        <h1 class="title">@yield('page_title', 'SIGEDC')</h1>
        @hasSection('page_actions')
            <div class="page-actions">@yield('page_actions')</div>
        @endif
    </section>

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
