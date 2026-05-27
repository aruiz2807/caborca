<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\PermissionCompatibilityService;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions:id,name')->get(['id', 'name', 'description']);
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        $configuredGroups = collect(config('permissions_catalog.groups', []));
        $permissionsByName = $permissions->keyBy('name');
        $configuredPermissionNames = $configuredGroups
            ->flatMap(fn (array $group) => $group['permissions'] ?? [])
            ->unique()
            ->values();

        $permissionGroups = $configuredGroups
            ->map(function (array $group, string $key) use ($permissionsByName) {
                $groupPermissions = collect($group['permissions'] ?? [])
                    ->map(fn (string $permissionName) => $permissionsByName->get($permissionName))
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'key' => $key,
                    'label' => $group['label'] ?? $key,
                    'permissions' => $groupPermissions,
                ];
            })
            ->filter(fn (array $group) => !empty($group['permissions']))
            ->values();

        $ungroupedPermissions = $permissions
            ->filter(fn (Permission $permission) => !$configuredPermissionNames->contains($permission->name))
            ->values();

        if ($ungroupedPermissions->isNotEmpty()) {
            $permissionGroups->push([
                'key' => 'other',
                'label' => 'Otros',
                'permissions' => $ungroupedPermissions->all(),
            ]);
        }

        $permissionCompatibilityRules = collect(config('permission_compatibility_map.rules', []))
            ->filter(fn ($rule) => is_array($rule))
            ->map(function (array $rule) {
                $ifAny = collect($rule['if_any'] ?? [])
                    ->filter(fn ($permissionName) => is_string($permissionName) && $permissionName !== '')
                    ->values()
                    ->all();

                $grant = collect($rule['grant'] ?? [])
                    ->filter(fn ($permissionName) => is_string($permissionName) && $permissionName !== '')
                    ->values()
                    ->all();

                return [
                    'if_any' => $ifAny,
                    'grant' => $grant,
                ];
            })
            ->filter(fn (array $rule) => !empty($rule['if_any']) && !empty($rule['grant']))
            ->values()
            ->all();

        return Inertia::render('Settings/Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionGroups' => $permissionGroups,
            'permissionCompatibilityRules' => $permissionCompatibilityRules,
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['string', 'max:255'],
        ])->validate();

        Role::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'guard_name' => 'web',
        ]);

        return to_route('roles.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255', $role->name !== $request->name ? Rule::unique('roles') : ''],
            'description' => ['string', 'max:255'],
        ])->validate();


        $role->forceFill([
            'name' => $request['name'],
            'description' => $request['description'],
        ])->save();

        return to_route('roles.index')->with('message', 'stored');
    }

    public function updatePermissions(Request $request, PermissionCompatibilityService $permissionCompatibilityService, $id)
    {
        $role = Role::findOrFail($id);

        Validator::make($request->input(), [
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name']
        ])->validate();

        $knownPermissions = Permission::query()->pluck('name')->values()->all();
        $requestedPermissions = $request->input('permissions', []);
        $resolvedPermissions = $permissionCompatibilityService->expand(
            is_array($requestedPermissions) ? $requestedPermissions : [],
            $knownPermissions
        );

        $role->syncPermissions($resolvedPermissions);

        return to_route('roles.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {
        $role = Role::find($id);

        //TODO delete role permissions
        $role->delete();

        return to_route('roles.index')->with('message', 'deleted');
    }
}
