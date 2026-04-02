<?php

namespace App\Modules\Shelter\Controllers;

use App\Core\Support\TenantContext;
use App\Http\Controllers\Controller;
use App\Modules\Shelter\Models\Shelter;
use App\Modules\Shelter\Repositories\ShelterRepository;
use App\Modules\Shelter\Requests\StoreShelterRequest;
use App\Modules\Shelter\Requests\UpdateShelterRequest;
use App\Modules\Shelter\Services\ShelterService;
use App\Modules\Territory\Models\Bairro;
use App\Modules\Territory\Models\Municipio;
use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShelterController extends Controller
{
    public function __construct(
        private readonly ShelterRepository $repository,
        private readonly ShelterService $service,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function index(Request $request): JsonResponse|View
    {
        $this->authorize('viewAny', Shelter::class);

        $tenantId = $this->resolveTenantIdOrAbort();
        $filters = $request->only(['municipio_id', 'bairro_id', 'territorial_unit_id', 'shelter_type', 'is_active']);

        $shelters = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $filters,
            perPage: (int) $request->integer('per_page', 15),
        );

        if ($request->expectsJson()) {
            return response()->json($shelters);
        }

        $units = TerritorialUnit::query()
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

        return view('shelter.shelters.index', [
            'shelters' => $shelters,
            'filters' => $filters,
            'units' => $units,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'shelterTypes' => Shelter::SHELTER_TYPES,
            'editingShelter' => null,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Shelter::class);

        /** @var View $view */
        $view = $this->index($request);

        return $view;
    }

    public function store(StoreShelterRequest $request): JsonResponse|RedirectResponse
    {
        $shelter = $this->service->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Abrigo cadastrado com sucesso.',
                'data' => $shelter,
            ], 201);
        }

        return redirect()
            ->route('shelters.index')
            ->with('success', 'Abrigo cadastrado com sucesso.');
    }

    public function edit(Request $request, Shelter $shelter): View
    {
        $this->authorize('update', $shelter);

        $tenantId = $this->resolveTenantIdOrAbort();
        $filters = $request->only(['municipio_id', 'bairro_id', 'territorial_unit_id', 'shelter_type', 'is_active']);

        $shelters = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $filters,
            perPage: (int) $request->integer('per_page', 15),
        );

        $units = TerritorialUnit::query()
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

        $selectedMunicipioId = (int) ($filters['municipio_id'] ?? $shelter->territorialUnit?->municipio_id ?? 0);
        $bairros = $selectedMunicipioId > 0
            ? Bairro::query()
                ->where('municipio_id', $selectedMunicipioId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(['id', 'nome'])
            : collect();

        return view('shelter.shelters.index', [
            'shelters' => $shelters,
            'filters' => $filters,
            'units' => $units,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'shelterTypes' => Shelter::SHELTER_TYPES,
            'editingShelter' => $shelter,
        ]);
    }

    public function update(UpdateShelterRequest $request, Shelter $shelter): JsonResponse|RedirectResponse
    {
        $updated = $this->service->update($shelter, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Abrigo atualizado com sucesso.',
                'data' => $updated,
            ]);
        }

        return redirect()
            ->route('shelters.index')
            ->with('success', 'Abrigo atualizado com sucesso.');
    }

    public function deactivate(Request $request, Shelter $shelter): JsonResponse|RedirectResponse
    {
        $this->authorize('deactivate', $shelter);
        $deactivated = $this->service->deactivate($shelter);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Abrigo inativado com sucesso.',
                'data' => $deactivated,
            ]);
        }

        return redirect()
            ->route('shelters.index')
            ->with('success', 'Abrigo inativado com sucesso.');
    }

    private function resolveTenantIdOrAbort(): int
    {
        $tenantId = $this->tenantContext->tenantId();
        abort_if($tenantId === null, 403, 'Tenant nao resolvido.');

        return $tenantId;
    }
}
