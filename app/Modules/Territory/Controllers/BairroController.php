<?php

namespace App\Modules\Territory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Territory\Models\Bairro;
use App\Modules\Territory\Models\Municipio;
use App\Modules\Territory\Repositories\BairroRepository;
use App\Modules\Territory\Requests\StoreBairroRequest;
use App\Modules\Territory\Requests\UpdateBairroRequest;
use App\Modules\Territory\Services\BairroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BairroController extends Controller
{
    public function __construct(
        private readonly BairroRepository $repository,
        private readonly BairroService $service,
    ) {
    }

    public function index(Request $request): JsonResponse|View
    {
        $this->authorize('viewAny', Bairro::class);

        $filters = $request->only(['municipio_id', 'nome', 'codigo_ibge', 'ativo']);
        $bairros = $this->repository->paginateByFilters(
            filters: $filters,
            perPage: (int) $request->integer('per_page', 15),
        );

        if ($request->expectsJson()) {
            return response()->json($bairros);
        }

        $municipios = Municipio::query()
            ->where('ativo', true)
            ->orderBy('uf')
            ->orderBy('nome')
            ->get(['id', 'nome', 'uf']);

        return view('territory.bairros.index', [
            'bairros' => $bairros,
            'filters' => $filters,
            'municipios' => $municipios,
            'editingBairro' => null,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Bairro::class);

        /** @var View $view */
        $view = $this->index($request);

        return $view;
    }

    public function store(StoreBairroRequest $request): JsonResponse|RedirectResponse
    {
        $bairro = $this->service->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Bairro cadastrado com sucesso.',
                'data' => $bairro,
            ], 201);
        }

        return redirect()
            ->route('territory.bairros.index', ['municipio_id' => $bairro->municipio_id])
            ->with('success', 'Bairro cadastrado com sucesso.');
    }

    public function edit(Request $request, Bairro $bairro): View
    {
        $this->authorize('update', $bairro);

        $filters = $request->only(['municipio_id', 'nome', 'codigo_ibge', 'ativo']);
        $bairros = $this->repository->paginateByFilters(
            filters: $filters,
            perPage: (int) $request->integer('per_page', 15),
        );

        $municipios = Municipio::query()
            ->where('ativo', true)
            ->orderBy('uf')
            ->orderBy('nome')
            ->get(['id', 'nome', 'uf']);

        return view('territory.bairros.index', [
            'bairros' => $bairros,
            'filters' => $filters,
            'municipios' => $municipios,
            'editingBairro' => $bairro,
        ]);
    }

    public function update(UpdateBairroRequest $request, Bairro $bairro): JsonResponse|RedirectResponse
    {
        $updated = $this->service->update($bairro, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Bairro atualizado com sucesso.',
                'data' => $updated,
            ]);
        }

        return redirect()
            ->route('territory.bairros.index', ['municipio_id' => $updated->municipio_id])
            ->with('success', 'Bairro atualizado com sucesso.');
    }
}
