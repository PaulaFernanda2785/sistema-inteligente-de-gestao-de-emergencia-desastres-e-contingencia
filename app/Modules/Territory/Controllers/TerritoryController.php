<?php

namespace App\Modules\Territory\Controllers;

use App\Core\Support\TenantContext;
use App\Http\Controllers\Controller;
use App\Modules\Territory\Models\Territory;
use App\Modules\Territory\Repositories\TerritoryRepository;
use App\Modules\Territory\Requests\StoreTerritoryRequest;
use App\Modules\Territory\Requests\UpdateTerritoryRequest;
use App\Modules\Territory\Services\TerritoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TerritoryController extends Controller
{
    public function __construct(
        private readonly TerritoryRepository $repository,
        private readonly TerritoryService $service,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function index(Request $request): JsonResponse|View
    {
        $this->authorize('viewAny', Territory::class);

        $tenantId = $this->resolveTenantIdOrAbort();
        $territories = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $request->only(['name', 'territory_type']),
            perPage: (int) $request->integer('per_page', 15),
        );

        if ($request->expectsJson()) {
            return response()->json($territories);
        }

        return view('territory.territories.index', [
            'territories' => $territories,
            'filters' => $request->only(['name', 'territory_type']),
            'editingTerritory' => null,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Territory::class);

        /** @var View $view */
        $view = $this->index($request);

        return $view;
    }

    public function store(StoreTerritoryRequest $request): JsonResponse|RedirectResponse
    {
        $territory = $this->service->createTerritory($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Territorio cadastrado com sucesso.',
                'data' => $territory,
            ], 201);
        }

        return redirect()
            ->route('territory.territories.index')
            ->with('success', 'Territorio cadastrado com sucesso.');
    }

    public function edit(Request $request, Territory $territory): View
    {
        $this->authorize('update', $territory);

        $tenantId = $this->resolveTenantIdOrAbort();
        $territories = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $request->only(['name', 'territory_type']),
            perPage: (int) $request->integer('per_page', 15),
        );

        return view('territory.territories.index', [
            'territories' => $territories,
            'filters' => $request->only(['name', 'territory_type']),
            'editingTerritory' => $territory,
        ]);
    }

    public function update(UpdateTerritoryRequest $request, Territory $territory): JsonResponse|RedirectResponse
    {
        $updated = $this->service->updateTerritory($territory, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Territorio atualizado com sucesso.',
                'data' => $updated,
            ]);
        }

        return redirect()
            ->route('territory.territories.index')
            ->with('success', 'Territorio atualizado com sucesso.');
    }

    private function resolveTenantIdOrAbort(): int
    {
        $tenantId = $this->tenantContext->tenantId();
        abort_if($tenantId === null, 403, 'Tenant nao resolvido.');

        return $tenantId;
    }
}
