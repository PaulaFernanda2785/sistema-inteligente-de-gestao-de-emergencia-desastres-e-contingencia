<?php

namespace App\Modules\Territory\Controllers;

use App\Core\Support\TenantContext;
use App\Http\Controllers\Controller;
use App\Modules\Territory\Models\Bairro;
use App\Modules\Territory\Models\Municipio;
use App\Modules\Territory\Models\TerritorialUnit;
use App\Modules\Territory\Models\Territory;
use App\Modules\Territory\Repositories\TerritorialUnitRepository;
use App\Modules\Territory\Requests\StoreTerritorialUnitRequest;
use App\Modules\Territory\Requests\UpdateTerritorialUnitRequest;
use App\Modules\Territory\Services\TerritoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TerritorialUnitController extends Controller
{
    public function __construct(
        private readonly TerritorialUnitRepository $repository,
        private readonly TerritoryService $service,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function index(Request $request): JsonResponse|View
    {
        $this->authorize('viewAny', TerritorialUnit::class);

        $tenantId = $this->resolveTenantIdOrAbort();
        $filters = $request->only(['territory_id', 'municipio_id', 'bairro_id', 'name', 'unit_type']);

        $units = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $filters,
            perPage: (int) $request->integer('per_page', 15),
        );

        if ($request->expectsJson()) {
            return response()->json($units);
        }

        $territories = Territory::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $municipios = Municipio::query()
            ->where('ativo', true)
            ->orderBy('uf')
            ->orderBy('nome')
            ->get(['id', 'nome', 'uf']);

        $selectedMunicipioId = (int) ($filters['municipio_id'] ?? 0);
        $bairros = $selectedMunicipioId > 0
            ? Bairro::query()
                ->where('municipio_id', $selectedMunicipioId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(['id', 'nome'])
            : collect();

        return view('territory.units.index', [
            'units' => $units,
            'filters' => $filters,
            'territories' => $territories,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'editingUnit' => null,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', TerritorialUnit::class);

        /** @var View $view */
        $view = $this->index($request);

        return $view;
    }

    public function store(StoreTerritorialUnitRequest $request): JsonResponse|RedirectResponse
    {
        $unit = $this->service->createUnit($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unidade territorial cadastrada com sucesso.',
                'data' => $unit,
            ], 201);
        }

        return redirect()
            ->route('territory.units.index', ['territory_id' => $unit->territory_id])
            ->with('success', 'Unidade territorial cadastrada com sucesso.');
    }

    public function edit(Request $request, TerritorialUnit $unit): View
    {
        $this->authorize('update', $unit);

        $tenantId = $this->resolveTenantIdOrAbort();
        $filters = $request->only(['territory_id', 'municipio_id', 'bairro_id', 'name', 'unit_type']);

        $units = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $filters,
            perPage: (int) $request->integer('per_page', 15),
        );

        $territories = Territory::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $municipios = Municipio::query()
            ->where('ativo', true)
            ->orderBy('uf')
            ->orderBy('nome')
            ->get(['id', 'nome', 'uf']);

        $selectedMunicipioId = (int) ($filters['municipio_id'] ?? $unit->municipio_id ?? 0);
        $bairros = $selectedMunicipioId > 0
            ? Bairro::query()
                ->where('municipio_id', $selectedMunicipioId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(['id', 'nome'])
            : collect();

        return view('territory.units.index', [
            'units' => $units,
            'filters' => $filters,
            'territories' => $territories,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'editingUnit' => $unit,
        ]);
    }

    public function update(UpdateTerritorialUnitRequest $request, TerritorialUnit $unit): JsonResponse|RedirectResponse
    {
        $updated = $this->service->updateUnit($unit, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unidade territorial atualizada com sucesso.',
                'data' => $updated,
            ]);
        }

        return redirect()
            ->route('territory.units.index', ['territory_id' => $updated->territory_id])
            ->with('success', 'Unidade territorial atualizada com sucesso.');
    }

    private function resolveTenantIdOrAbort(): int
    {
        $tenantId = $this->tenantContext->tenantId();
        abort_if($tenantId === null, 403, 'Tenant nao resolvido.');

        return $tenantId;
    }
}
