<?php

namespace App\Modules\Risk\Controllers;

use App\Core\Support\TenantContext;
use App\Http\Controllers\Controller;
use App\Modules\Risk\Models\RiskArea;
use App\Modules\Risk\Repositories\RiskAreaRepository;
use App\Modules\Risk\Requests\StoreRiskAreaRequest;
use App\Modules\Risk\Requests\UpdateRiskAreaRequest;
use App\Modules\Risk\Services\RiskAreaService;
use App\Modules\Territory\Models\Bairro;
use App\Modules\Territory\Models\Municipio;
use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiskAreaController extends Controller
{
    public function __construct(
        private readonly RiskAreaRepository $repository,
        private readonly RiskAreaService $service,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function index(Request $request): JsonResponse|View
    {
        $this->authorize('viewAny', RiskArea::class);

        $tenantId = $this->resolveTenantIdOrAbort();
        $filters = $request->only(['municipio_id', 'bairro_id', 'territorial_unit_id', 'risk_type', 'priority_level', 'is_active']);

        $areas = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $filters,
            perPage: (int) $request->integer('per_page', 15),
        );

        if ($request->expectsJson()) {
            return response()->json($areas);
        }

        $territorialUnits = TerritorialUnit::query()
            ->where('tenant_id', $tenantId)
            ->when(
                $filters['municipio_id'] ?? null,
                fn ($query, int|string $value) => $query->where('municipio_id', (int) $value),
            )
            ->when(
                $filters['bairro_id'] ?? null,
                fn ($query, int|string $value) => $query->where('bairro_id', (int) $value),
            )
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

        return view('risk.areas.index', [
            'areas' => $areas,
            'filters' => $filters,
            'territorialUnits' => $territorialUnits,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'riskTypes' => RiskArea::RISK_TYPES,
            'priorityLevels' => RiskArea::PRIORITY_LEVELS,
            'editingArea' => null,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', RiskArea::class);

        /** @var View $view */
        $view = $this->index($request);

        return $view;
    }

    public function store(StoreRiskAreaRequest $request): JsonResponse|RedirectResponse
    {
        $area = $this->service->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Area de risco cadastrada com sucesso.',
                'data' => $area,
            ], 201);
        }

        return redirect()
            ->route('risk.areas.index')
            ->with('success', 'Area de risco cadastrada com sucesso.');
    }

    public function edit(Request $request, RiskArea $risk_area): View
    {
        $this->authorize('update', $risk_area);

        $tenantId = $this->resolveTenantIdOrAbort();
        $filters = $request->only(['municipio_id', 'bairro_id', 'territorial_unit_id', 'risk_type', 'priority_level', 'is_active']);

        $areas = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $filters,
            perPage: (int) $request->integer('per_page', 15),
        );

        $territorialUnits = TerritorialUnit::query()
            ->where('tenant_id', $tenantId)
            ->when(
                $filters['municipio_id'] ?? null,
                fn ($query, int|string $value) => $query->where('municipio_id', (int) $value),
            )
            ->when(
                $filters['bairro_id'] ?? null,
                fn ($query, int|string $value) => $query->where('bairro_id', (int) $value),
            )
            ->orderBy('name')
            ->get(['id', 'name']);

        $municipios = Municipio::query()
            ->where('ativo', true)
            ->orderBy('uf')
            ->orderBy('nome')
            ->get(['id', 'nome', 'uf']);

        $selectedMunicipioId = (int) ($filters['municipio_id'] ?? $risk_area->territorialUnit?->municipio_id ?? 0);
        $bairros = $selectedMunicipioId > 0
            ? Bairro::query()
                ->where('municipio_id', $selectedMunicipioId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(['id', 'nome'])
            : collect();

        return view('risk.areas.index', [
            'areas' => $areas,
            'filters' => $filters,
            'territorialUnits' => $territorialUnits,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'riskTypes' => RiskArea::RISK_TYPES,
            'priorityLevels' => RiskArea::PRIORITY_LEVELS,
            'editingArea' => $risk_area,
        ]);
    }

    public function update(UpdateRiskAreaRequest $request, RiskArea $risk_area): JsonResponse|RedirectResponse
    {
        $updated = $this->service->update($risk_area, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Area de risco atualizada com sucesso.',
                'data' => $updated,
            ]);
        }

        return redirect()
            ->route('risk.areas.index')
            ->with('success', 'Area de risco atualizada com sucesso.');
    }

    public function deactivate(Request $request, RiskArea $risk_area): JsonResponse|RedirectResponse
    {
        $this->authorize('deactivate', $risk_area);
        $deactivated = $this->service->deactivate($risk_area);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Area de risco inativada com sucesso.',
                'data' => $deactivated,
            ]);
        }

        return redirect()
            ->route('risk.areas.index')
            ->with('success', 'Area de risco inativada com sucesso.');
    }

    private function resolveTenantIdOrAbort(): int
    {
        $tenantId = $this->tenantContext->tenantId();
        abort_if($tenantId === null, 403, 'Tenant nao resolvido.');

        return $tenantId;
    }
}
