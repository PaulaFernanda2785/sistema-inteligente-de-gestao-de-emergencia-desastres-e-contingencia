<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Support\TenantContext;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\User;
use App\Modules\Admin\Repositories\UserRepository;
use App\Modules\Admin\Requests\StoreUserRequest;
use App\Modules\Admin\Requests\UpdateUserRequest;
use App\Modules\Admin\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserManagementService $service,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $tenantId = $this->tenantContext->tenantId();
        abort_if($tenantId === null, 403, 'Tenant não resolvido.');

        $users = $this->repository->paginateByFilters(
            tenantId: $tenantId,
            filters: $request->only(['name', 'email', 'status']),
            perPage: (int) $request->integer('per_page', 15),
        );

        return response()->json($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $createdUser = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso.',
            'data' => $createdUser,
        ], 201);
    }

    public function update(UpdateUserRequest $request, int $user): JsonResponse
    {
        $target = User::query()
            ->where('tenant_id', $this->tenantContext->tenantId())
            ->findOrFail($user);

        $this->authorize('update', $target);

        $updatedUser = $this->service->update($target->id, $request->validated());

        return response()->json([
            'message' => 'Usuário atualizado com sucesso.',
            'data' => $updatedUser,
        ]);
    }

    public function deactivate(Request $request, int $user): JsonResponse
    {
        $target = User::query()
            ->where('tenant_id', $this->tenantContext->tenantId())
            ->findOrFail($user);

        $this->authorize('deactivate', $target);

        $deactivatedUser = $this->service->deactivate($target->id, (int) $request->user()->id);

        return response()->json([
            'message' => 'Usuário inativado com sucesso.',
            'data' => $deactivatedUser,
        ]);
    }
}
