
---

## 1. Finalidade do documento

Este documento define o esqueleto real do backend do sistema em Laravel, estabelecendo:

- estrutura de pastas;
    
- organização modular;
    
- convenções de namespaces;
    
- rotas-base;
    
- controllers;
    
- requests;
    
- services;
    
- repositories;
    
- models;
    
- policies;
    
- middleware;
    
- fluxo de implementação técnica inicial.
    

O objetivo é permitir que a equipe inicie o desenvolvimento com base concreta, padronizada e coerente com toda a documentação anterior.

---

## 2. Stack técnica assumida

### Backend

- PHP 8.3+
    
- Laravel 11+
    

### Banco

- PostgreSQL 15+
    
- PostGIS
    

### Apoio técnico

- Laravel Eloquent
    
- Form Requests
    
- Policies / Gates
    
- Service Layer própria
    
- Repositories para consultas complexas
    
- Blade + Livewire no MVP web
    

---

## 3. Diretrizes de implementação

### 3.1 Regra central

Controller não concentra regra de negócio.

### 3.2 Regra de domínio

A regra de negócio deve residir em Services.

### 3.3 Regra de acesso

Permissões devem ser aplicadas via Policies, middleware e gates.

### 3.4 Regra de tenant

Toda consulta e persistência de domínio institucional ou operacional deve respeitar o contexto do tenant.

### 3.5 Regra de auditoria

Toda ação crítica deve chamar mecanismo centralizado de auditoria.

---

## 4. Estrutura real sugerida do projeto

```text
app/
  Core/
    Http/
      Controllers/
        Controller.php
    Support/
      ApiResponse.php
      AuditLogger.php
      TenantContext.php
    Services/
      BaseService.php
    Exceptions/
      DomainException.php
      AuthorizationException.php
      ValidationException.php

  Modules/
    Auth/
      Controllers/
      Requests/
      Services/
      Actions/
      Routes/

    Tenancy/
      Middleware/
      Models/
      Services/
      Policies/

    Admin/
      Controllers/
      Requests/
      Services/
      Repositories/
      Models/
      Policies/
      DTOs/
      Routes/

    Territory/
      Controllers/
      Requests/
      Services/
      Repositories/
      Models/
      Policies/
      DTOs/
      Routes/

    Risk/
      Controllers/
      Requests/
      Services/
      Repositories/
      Models/
      Policies/
      DTOs/
      Routes/

    ContingencyPlan/
      Controllers/
      Requests/
      Services/
      Repositories/
      Models/
      Policies/
      DTOs/
      Routes/
      Reports/

    DisasterEvent/
      Controllers/
      Requests/
      Services/
      Repositories/
      Models/
      Policies/
      DTOs/
      Routes/

    Command/
      Controllers/
      Requests/
      Services/
      Repositories/
      Models/
      Policies/
      DTOs/
      Routes/

    Operations/
      Controllers/
      Requests/
      Services/
      Repositories/
      Models/
      Policies/
      DTOs/
      Routes/

    Reports/
      Controllers/
      Services/
      Reports/
      DTOs/
      Routes/

    Audit/
      Controllers/
      Services/
      Repositories/
      Models/
      Policies/
      Routes/

bootstrap/
routes/
config/
database/
  migrations/
  seeders/
resources/
  views/
storage/
tests/
```

---

## 5. Convenções de namespaces

### Exemplo de namespaces

```php
namespace App\Modules\Admin\Controllers;
namespace App\Modules\Admin\Services;
namespace App\Modules\Admin\Requests;
namespace App\Modules\Admin\Repositories;
namespace App\Modules\Admin\Models;
namespace App\Modules\Admin\Policies;
```

### Regra de nomeação

- Controllers: `UserController`, `OrganizationController`
    
- Requests: `StoreUserRequest`, `UpdateUserRequest`
    
- Services: `UserManagementService`
    
- Repositories: `UserRepository`
    
- Policies: `UserPolicy`
    
- Models: `User`
    
- DTOs: `UserData`, `OrganizationData`
    

---

## 6. Arquivo-base de rotas do sistema

## 6.1 `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    require app_path('Modules/Auth/Routes/web.php');

    Route::middleware(['auth', 'tenant.context'])->group(function () {
        require app_path('Modules/Admin/Routes/web.php');
        require app_path('Modules/Territory/Routes/web.php');
        require app_path('Modules/Risk/Routes/web.php');
        require app_path('Modules/ContingencyPlan/Routes/web.php');
        require app_path('Modules/DisasterEvent/Routes/web.php');
        require app_path('Modules/Command/Routes/web.php');
        require app_path('Modules/Operations/Routes/web.php');
        require app_path('Modules/Reports/Routes/web.php');
        require app_path('Modules/Audit/Routes/web.php');
    });
});
```

---

## 7. Middleware críticos

## 7.1 Middleware `SetTenantContext`

### Finalidade

Resolver o tenant ativo do usuário autenticado e armazenar o contexto para toda a requisição.

### Responsabilidades

- identificar tenant do usuário;
    
- validar se tenant está ativo;
    
- carregar contexto institucional;
    
- disponibilizar tenant em singleton/helper;
    
- bloquear acesso se o tenant estiver inválido.
    

### Exemplo de estrutura

```php
<?php

namespace App\Modules\Tenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Core\Support\TenantContext;

class SetTenantContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->tenant || !$user->tenant->is_active) {
            abort(403, 'Tenant inválido ou inativo.');
        }

        app(TenantContext::class)->setTenant($user->tenant);

        return $next($request);
    }
}
```

---

## 7.2 Middleware `EnsurePermission`

### Finalidade

Garantir que o usuário possua permissão específica para determinada rota/ação.

### Exemplo de assinatura

```php
->middleware('permission:users.create')
```

---

## 8. Núcleo de suporte do sistema

## 8.1 `TenantContext`

```php
<?php

namespace App\Core\Support;

use App\Modules\Tenancy\Models\Tenant;

class TenantContext
{
    protected ?Tenant $tenant = null;

    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function tenantId(): ?int
    {
        return $this->tenant?->id;
    }
}
```

---

## 8.2 `BaseService`

```php
<?php

namespace App\Core\Services;

use App\Core\Support\TenantContext;
use App\Core\Support\AuditLogger;

abstract class BaseService
{
    public function __construct(
        protected TenantContext $tenantContext,
        protected AuditLogger $auditLogger,
    ) {}

    protected function tenantId(): int
    {
        return $this->tenantContext->tenantId();
    }
}
```

---

## 8.3 `AuditLogger`

```php
<?php

namespace App\Core\Support;

use App\Modules\Audit\Models\AuditLog;

class AuditLogger
{
    public function log(string $module, string $action, ?string $entityType = null, ?int $entityId = null, array $oldValues = [], array $newValues = []): void
    {
        AuditLog::create([
            'tenant_id'   => app(TenantContext::class)->tenantId(),
            'user_id'     => auth()->id(),
            'event_type'  => 'system_action',
            'module'      => $module,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_values'  => empty($oldValues) ? null : $oldValues,
            'new_values'  => empty($newValues) ? null : $newValues,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
```

---

## 9. Models-base prioritários do MVP

## 9.1 `Tenant`

```php
<?php

namespace App\Modules\Tenancy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Admin\Models\User;

class Tenant extends Model
{
    protected $table = 'tenants';

    protected $fillable = [
        'uuid',
        'legal_name',
        'trade_name',
        'tenant_type',
        'document_number',
        'state_code',
        'city_name',
        'plan_type',
        'subscription_status',
        'contract_start_date',
        'contract_end_date',
        'is_active',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date'   => 'date',
        'is_active'           => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
```

---

## 9.2 `User`

```php
<?php

namespace App\Modules\Admin\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Tenancy\Models\Tenant;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'unit_id',
        'name',
        'email',
        'cpf_hash',
        'password_hash',
        'phone',
        'position_name',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

---

## 9.3 `DisasterEvent`

```php
<?php

namespace App\Modules\DisasterEvent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Tenancy\Models\Tenant;

class DisasterEvent extends Model
{
    protected $table = 'disaster_events';

    protected $fillable = [
        'tenant_id',
        'territory_id',
        'territorial_unit_id',
        'event_code',
        'title',
        'disaster_typology_id',
        'severity_level_id',
        'contingency_plan_version_id',
        'risk_scenario_id',
        'event_status',
        'operational_phase',
        'started_at',
        'ended_at',
        'summary_description',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

---

## 10. Repositories prioritários

Repositories devem ser usados principalmente para:

- filtros complexos;
    
- consultas com muitos joins;
    
- dashboards;
    
- relatórios;
    
- buscas paginadas com múltiplos critérios.
    

### Exemplo: `UserRepository`

```php
<?php

namespace App\Modules\Admin\Repositories;

use App\Modules\Admin\Models\User;

class UserRepository
{
    public function paginateByFilters(int $tenantId, array $filters = [], int $perPage = 15)
    {
        return User::query()
            ->where('tenant_id', $tenantId)
            ->when($filters['name'] ?? null, fn ($q, $value) => $q->where('name', 'ilike', "%{$value}%"))
            ->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }
}
```

---

## 11. Requests prioritários

Todos os formulários críticos devem usar Form Request.

### Exemplo: `StoreUserRequest`

```php
<?php

namespace App\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.create');
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:200'],
            'email'           => ['required', 'email', 'max:150'],
            'organization_id' => ['required', 'integer'],
            'unit_id'         => ['nullable', 'integer'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'position_name'   => ['nullable', 'string', 'max:150'],
            'status'          => ['required', 'string', 'max:30'],
            'password'        => ['required', 'string', 'min:8'],
            'roles'           => ['required', 'array', 'min:1'],
        ];
    }
}
```

---

## 12. Policies prioritárias

### Policies mínimas do MVP

- `UserPolicy`
    
- `OrganizationPolicy`
    
- `TerritoryPolicy`
    
- `RiskAreaPolicy`
    
- `ContingencyPlanPolicy`
    
- `DisasterEventPolicy`
    
- `OperationalOccurrencePolicy`
    
- `MissionPolicy`
    
- `AuditLogPolicy`
    

### Exemplo: `UserPolicy`

```php
<?php

namespace App\Modules\Admin\Policies;

use App\Modules\Admin\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $target): bool
    {
        return $user->tenant_id === $target->tenant_id && $user->can('users.update');
    }

    public function deactivate(User $user, User $target): bool
    {
        return $user->tenant_id === $target->tenant_id && $user->can('users.deactivate');
    }
}
```

---

## 13. Controllers prioritários do MVP

## 13.1 Auth

- `LoginController`
    
- `LogoutController`
    

## 13.2 Admin

- `DashboardController`
    
- `UserController`
    
- `RoleController`
    
- `OrganizationController`
    
- `StrategicContactController`
    

## 13.3 Territory / Risk

- `TerritoryController`
    
- `TerritorialUnitController`
    
- `RiskAreaController`
    
- `RiskScenarioController`
    
- `ShelterController`
    
- `SupportPointController`
    
- `OperationalBaseController`
    

## 13.4 Contingency Plan

- `ContingencyPlanController`
    
- `ContingencyPlanVersionController`
    
- `ContingencyPlanSectionController`
    
- `ResponsibilityMatrixController`
    
- `ActivationProtocolController`
    
- `ContingencyPlanPublicationController`
    

## 13.5 Disaster Event

- `DisasterEventController`
    
- `DisasterEventStatusController`
    
- `DisasterEventTimelineController`
    
- `DisasterEventClosureController`
    

## 13.6 Command

- `CommandStructureController`
    
- `CommandPositionController`
    
- `OperationalObjectiveController`
    
- `OperationalDecisionController`
    

## 13.7 Operations

- `OperationalOccurrenceController`
    
- `MissionController`
    
- `FieldTeamController`
    
- `OperationalResourceController`
    
- `DamageAssessmentController`
    
- `NeedsAssessmentController`
    
- `OperationalLogController`
    

## 13.8 Reports / Audit

- `EventReportController`
    
- `GeneratedReportController`
    
- `AuditLogController`
    

---

## 14. Services prioritários do MVP

## 14.1 Fundação e segurança

- `TenantService`
    
- `UserManagementService`
    
- `RolePermissionService`
    
- `SessionManagementService`
    
- `AuditLogService`
    

## 14.2 Base territorial

- `TerritoryService`
    
- `RiskAreaService`
    
- `RiskScenarioService`
    
- `ShelterService`
    
- `GeoProcessingService`
    

## 14.3 Plano

- `ContingencyPlanService`
    
- `PlanVersioningService`
    
- `ContingencyPlanBuilderService`
    
- `ResponsibilityMatrixService`
    
- `ActivationProtocolService`
    
- `PlanPublishingService`
    
- `PlanPdfService`
    

## 14.4 Evento e comando

- `DisasterEventService`
    
- `EventStatusService`
    
- `EventTimelineService`
    
- `EventClosureService`
    
- `CommandStructureService`
    
- `OperationalObjectiveService`
    
- `OperationalDecisionService`
    

## 14.5 Operações

- `OperationalOccurrenceService`
    
- `MissionService`
    
- `FieldTeamService`
    
- `OperationalResourceService`
    
- `DamageAssessmentService`
    
- `NeedsAssessmentService`
    
- `OperationalLogService`
    

## 14.6 Relatórios

- `EventReportService`
    
- `GeneratedReportService`
    

---

## 15. Exemplo real de Controller + Service

## 15.1 `UserController`

```php
<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Admin\Requests\StoreUserRequest;
use App\Modules\Admin\Requests\UpdateUserRequest;
use App\Modules\Admin\Services\UserManagementService;
use App\Modules\Admin\Repositories\UserRepository;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $repository,
        protected UserManagementService $service,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', auth()->user());

        $users = $this->repository->paginateByFilters(
            tenant_id: app(\App\Core\Support\TenantContext::class)->tenantId(),
            filters: request()->all(),
            perPage: 15,
        );

        return view('admin.users.index', compact('users'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function deactivate(int $id)
    {
        $this->service->deactivate($id);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário inativado com sucesso.');
    }
}
```

---

## 15.2 `UserManagementService`

```php
<?php

namespace App\Modules\Admin\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Core\Services\BaseService;
use App\Modules\Admin\Models\User;

class UserManagementService extends BaseService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'tenant_id'       => $this->tenantId(),
                'organization_id' => $data['organization_id'],
                'unit_id'         => $data['unit_id'] ?? null,
                'name'            => $data['name'],
                'email'           => $data['email'],
                'phone'           => $data['phone'] ?? null,
                'position_name'   => $data['position_name'] ?? null,
                'status'          => $data['status'],
                'password_hash'   => Hash::make($data['password']),
            ]);

            if (!empty($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

            $this->auditLogger->log(
                module: 'admin.users',
                action: 'create',
                entityType: User::class,
                entityId: $user->id,
                newValues: $user->toArray(),
            );

            return $user;
        });
    }

    public function update(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::query()
                ->where('tenant_id', $this->tenantId())
                ->findOrFail($id);

            $oldValues = $user->toArray();

            $user->update([
                'organization_id' => $data['organization_id'],
                'unit_id'         => $data['unit_id'] ?? null,
                'name'            => $data['name'],
                'email'           => $data['email'],
                'phone'           => $data['phone'] ?? null,
                'position_name'   => $data['position_name'] ?? null,
                'status'          => $data['status'],
            ]);

            if (isset($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

            $this->auditLogger->log(
                module: 'admin.users',
                action: 'update',
                entityType: User::class,
                entityId: $user->id,
                oldValues: $oldValues,
                newValues: $user->fresh()->toArray(),
            );

            return $user->fresh();
        });
    }

    public function deactivate(int $id): void
    {
        DB::transaction(function () use ($id) {
            $user = User::query()
                ->where('tenant_id', $this->tenantId())
                ->findOrFail($id);

            $oldValues = $user->toArray();

            $user->update([
                'status'     => 'INATIVO',
                'deleted_at' => now(),
            ]);

            $this->auditLogger->log(
                module: 'admin.users',
                action: 'deactivate',
                entityType: User::class,
                entityId: $user->id,
                oldValues: $oldValues,
                newValues: $user->fresh()->toArray(),
            );
        });
    }
}
```

---

## 16. Relacionamentos mínimos que precisam existir nos Models

### User

- `tenant()`
    
- `organization()`
    
- `unit()`
    
- `roles()`
    

### Organization

- `tenant()`
    
- `units()`
    
- `users()`
    

### Territory

- `tenant()`
    
- `units()`
    
- `plans()`
    
- `events()`
    

### ContingencyPlan

- `tenant()`
    
- `territory()`
    
- `versions()`
    
- `currentVersion()`
    

### DisasterEvent

- `tenant()`
    
- `territory()`
    
- `territorialUnit()`
    
- `severityLevel()`
    
- `typology()`
    
- `statusHistory()`
    
- `timeline()`
    
- `commandStructures()`
    
- `objectives()`
    
- `occurrences()`
    
- `missions()`
    
- `damageAssessments()`
    
- `needsAssessments()`
    

---

## 17. Rotas reais sugeridas por módulo

## 17.1 Auth

```php
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');
```

## 17.2 Admin

```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::patch('users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    Route::resource('organizations', OrganizationController::class)->except(['show', 'destroy']);
    Route::resource('contacts', StrategicContactController::class)->except(['show', 'destroy']);
});
```

## 17.3 Territory

```php
Route::prefix('territory')->name('territory.')->group(function () {
    Route::resource('territories', TerritoryController::class)->except(['show', 'destroy']);
    Route::resource('units', TerritorialUnitController::class)->except(['show', 'destroy']);
});
```

## 17.4 Risk

```php
Route::resource('risk-areas', RiskAreaController::class)->except(['show', 'destroy']);
Route::resource('risk-scenarios', RiskScenarioController::class)->except(['show', 'destroy']);
Route::resource('shelters', ShelterController::class)->except(['show', 'destroy']);
Route::resource('support-points', SupportPointController::class)->except(['show', 'destroy']);
Route::resource('operational-bases', OperationalBaseController::class)->except(['show', 'destroy']);
```

## 17.5 Contingency Plan

```php
Route::resource('contingency-plans', ContingencyPlanController::class)->except(['destroy']);
Route::resource('contingency-plan-versions', ContingencyPlanVersionController::class)->except(['destroy']);
Route::post('contingency-plan-versions/{id}/clone', [ContingencyPlanVersionController::class, 'clone'])->name('contingency-plan-versions.clone');
Route::post('contingency-plan-versions/{id}/submit', [ContingencyPlanPublicationController::class, 'submit'])->name('contingency-plan-versions.submit');
Route::post('contingency-plan-versions/{id}/approve', [ContingencyPlanPublicationController::class, 'approve'])->name('contingency-plan-versions.approve');
Route::post('contingency-plan-versions/{id}/publish', [ContingencyPlanPublicationController::class, 'publish'])->name('contingency-plan-versions.publish');
```

## 17.6 Disaster Event

```php
Route::resource('disaster-events', DisasterEventController::class)->except(['destroy']);
Route::post('disaster-events/{id}/status', [DisasterEventStatusController::class, 'update'])->name('disaster-events.status.update');
Route::post('disaster-events/{id}/timeline', [DisasterEventTimelineController::class, 'store'])->name('disaster-events.timeline.store');
Route::post('disaster-events/{id}/close', [DisasterEventClosureController::class, 'store'])->name('disaster-events.close');
```

## 17.7 Command

```php
Route::prefix('command')->name('command.')->group(function () {
    Route::post('{eventId}/structures', [CommandStructureController::class, 'store'])->name('structures.store');
    Route::post('structures/{id}/positions', [CommandPositionController::class, 'store'])->name('positions.store');
    Route::post('positions/{id}/assign', [CommandPositionController::class, 'assign'])->name('positions.assign');
    Route::resource('{eventId}/objectives', OperationalObjectiveController::class)->except(['show', 'destroy']);
    Route::resource('{eventId}/decisions', OperationalDecisionController::class)->except(['show', 'destroy']);
});
```

## 17.8 Operations

```php
Route::prefix('operations/{eventId}')->name('operations.')->group(function () {
    Route::resource('occurrences', OperationalOccurrenceController::class)->except(['show', 'destroy']);
    Route::resource('missions', MissionController::class)->except(['show', 'destroy']);
    Route::post('missions/{id}/assign', [MissionController::class, 'assign'])->name('missions.assign');
    Route::resource('teams', FieldTeamController::class)->except(['show', 'destroy']);
    Route::resource('resources', OperationalResourceController::class)->except(['show', 'destroy']);
    Route::resource('damages', DamageAssessmentController::class)->except(['show', 'destroy']);
    Route::resource('needs', NeedsAssessmentController::class)->except(['show', 'destroy']);
    Route::resource('logs', OperationalLogController::class)->only(['index', 'store']);
});
```

## 17.9 Reports / Audit

```php
Route::get('reports/events/{eventId}', [EventReportController::class, 'show'])->name('reports.events.show');
Route::post('reports/events/{eventId}/generate', [EventReportController::class, 'generate'])->name('reports.events.generate');
Route::get('reports/generated', [GeneratedReportController::class, 'index'])->name('reports.generated.index');
Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');
```

---

## 18. Ordem real de implementação do backend

### Fase 1 — Fundação

1. autenticação
    
2. tenant context
    
3. permissions / policies
    
4. audit logger
    

### Fase 2 — Administração

5. organizations
    
6. users
    
7. roles and permissions
    
8. strategic contacts
    

### Fase 3 — Território

9. territories
    
10. territorial units
    
11. risk areas
    
12. shelters
    

### Fase 4 — Plano

13. contingency plans
    
14. versions
    
15. sections
    
16. responsibilities
    
17. protocols
    
18. publication flow
    

### Fase 5 — Evento

19. disaster events
    
20. status history
    
21. timeline
    
22. closure
    

### Fase 6 — Comando e Operação

23. command structures
    
24. command assignments
    
25. objectives
    
26. occurrences
    
27. missions
    
28. damages
    
29. needs
    
30. reports
    

---

## 19. Definição de pronto do backend do MVP

O backend do MVP será considerado estruturalmente pronto quando possuir:

1. autenticação funcional;
    
2. isolamento por tenant;
    
3. permissão por policy/middleware;
    
4. CRUD real de usuários e organização;
    
5. CRUD real de território e risco;
    
6. CRUD real de plano e versões;
    
7. fluxo de publicação do plano;
    
8. abertura e encerramento de evento;
    
9. comando operacional básico;
    
10. objetivos, ocorrências e missões;
    
11. danos e necessidades;
    
12. geração de relatório por evento;
    
13. auditoria nas ações críticas.
    

---

## 20. Próximo artefato derivado mais útil

A partir deste esqueleto, os próximos artefatos tecnicamente mais úteis são:

1. **estrutura real de migrations em Laravel**;
    
2. **esqueleto real dos módulos em arquivos PHP prontos**;
    
3. **wireframes das telas críticas do MVP**;
    
4. **plano de testes técnicos do backend**.
    

---

## 21. Conclusão técnica

Este documento fecha a transição entre planejamento e construção.

A partir dele, a equipe já consegue iniciar a base do projeto Laravel de maneira disciplinada, sem depender de interpretação solta sobre nomes de classes, responsabilidades, estrutura de pastas ou sequência de implementação.

O próximo passo mais útil, agora sem dúvida, é produzir os **arquivos-base reais do backend em Laravel**, começando por:

- `web.php`
    
- middleware de tenant
    
- models principais
    
- requests principais
    
- `UserController`
    
- `UserManagementService`
    
- `DisasterEventController`
    
- `DisasterEventService`
    
- `ContingencyPlanController`
    
- `PlanPublishingService`
    

Esse é o ponto em que o projeto efetivamente começa a virar código.