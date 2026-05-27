<?php

namespace App\Services;

use App\Models\BiReport;
use App\Models\BiSection;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class BiPermissionService
{
    public function ensureSectionPermission(BiSection $section): Permission
    {
        return Permission::query()->firstOrCreate([
            'name' => $section->permissionName(),
            'guard_name' => 'web',
        ]);
    }

    public function ensureReportPermission(BiReport $report): Permission
    {
        return Permission::query()->firstOrCreate([
            'name' => $report->permissionName(),
            'guard_name' => 'web',
        ]);
    }

    /**
     * @param  array<int, int|string>  $roleIds
     * @param  array<int, int|string>  $userIds
     */
    public function syncReportAccess(BiReport $report, array $roleIds = [], array $userIds = []): void
    {
        DB::transaction(function () use ($report, $roleIds, $userIds): void {
            $reportPermission = $this->ensureReportPermission($report);
            $this->ensureSectionPermission($report->section);

            $normalizedRoleIds = $this->normalizeIds($roleIds);
            $normalizedUserIds = $this->normalizeIds($userIds);

            $roleModels = Role::query()->whereIn('id', $normalizedRoleIds)->get(['id', 'name', 'guard_name']);

            $reportPermission->syncRoles($roleModels);
            $this->syncPermissionUsers($reportPermission, $normalizedUserIds);

            $this->rebuildSectionPermission($report->section);
        });
    }

    public function rebuildSectionPermission(BiSection $section): void
    {
        $sectionPermission = $this->ensureSectionPermission($section);

        $reportPermissionNames = $section->reports()
            ->get(['id'])
            ->map(fn (BiReport $report) => $report->permissionName())
            ->values()
            ->all();

        if (empty($reportPermissionNames)) {
            $sectionPermission->syncRoles([]);
            $this->syncPermissionUsers($sectionPermission, []);

            return;
        }

        $reportPermissions = Permission::query()
            ->whereIn('name', $reportPermissionNames)
            ->with([
                'roles:id,name,guard_name',
            ])
            ->get();

        $roleIds = $reportPermissions
            ->flatMap(fn (Permission $permission) => $permission->roles->pluck('id'))
            ->unique()
            ->values()
            ->all();

        $userIds = $this->getAssignedUserIdsForPermissions($reportPermissions->pluck('id')->all());

        $sectionPermission->syncRoles($roleIds);
        $this->syncPermissionUsers($sectionPermission, $userIds);
    }

    public function deleteReport(BiReport $report): void
    {
        DB::transaction(function () use ($report): void {
            $section = $report->section;

            $this->deletePermissionByName($report->permissionName());
            $report->delete();

            if ($section) {
                $this->rebuildSectionPermission($section);
            }
        });
    }

    public function deleteSection(BiSection $section): void
    {
        DB::transaction(function () use ($section): void {
            $reports = $section->reports()->get(['id']);

            foreach ($reports as $report) {
                $this->deletePermissionByName($report->permissionName());
            }

            $this->deletePermissionByName($section->permissionName());
            $section->delete();
        });
    }

    protected function deletePermissionByName(string $permissionName): void
    {
        $permission = Permission::query()
            ->where('name', $permissionName)
            ->where('guard_name', 'web')
            ->first();

        if (! $permission) {
            return;
        }

        $permission->roles()->detach();
        $this->detachPermissionUsers($permission);
        $permission->delete();
    }

    /**
     * @param  array<int, int>  $userIds
     */
    protected function syncPermissionUsers(Permission $permission, array $userIds): void
    {
        $table = config('permission.table_names.model_has_permissions', 'model_has_permissions');
        $permissionColumn = config('permission.column_names.permission_pivot_key') ?: 'permission_id';
        $modelColumn = config('permission.column_names.model_morph_key') ?: 'model_id';
        $teamsEnabled = (bool) config('permission.teams', false);
        $teamKey = config('permission.column_names.team_foreign_key') ?: 'team_id';

        DB::table($table)
            ->where($permissionColumn, $permission->id)
            ->where('model_type', User::class)
            ->delete();

        if (empty($userIds)) {
            return;
        }

        $rows = collect($userIds)
            ->unique()
            ->map(function (int $userId) use ($permission, $permissionColumn, $modelColumn, $teamsEnabled, $teamKey): array {
                $row = [
                    $permissionColumn => $permission->id,
                    'model_type' => User::class,
                    $modelColumn => $userId,
                ];

                if ($teamsEnabled) {
                    $row[$teamKey] = getPermissionsTeamId();
                }

                return $row;
            })
            ->values()
            ->all();

        DB::table($table)->insert($rows);
    }

    protected function detachPermissionUsers(Permission $permission): void
    {
        $table = config('permission.table_names.model_has_permissions', 'model_has_permissions');
        $permissionColumn = config('permission.column_names.permission_pivot_key') ?: 'permission_id';

        DB::table($table)
            ->where($permissionColumn, $permission->id)
            ->where('model_type', User::class)
            ->delete();
    }

    /**
     * @param  array<int, int>  $permissionIds
     * @return array<int, int>
     */
    protected function getAssignedUserIdsForPermissions(array $permissionIds): array
    {
        if (empty($permissionIds)) {
            return [];
        }

        $table = config('permission.table_names.model_has_permissions', 'model_has_permissions');
        $permissionColumn = config('permission.column_names.permission_pivot_key') ?: 'permission_id';
        $modelColumn = config('permission.column_names.model_morph_key') ?: 'model_id';

        return DB::table($table)
            ->whereIn($permissionColumn, $permissionIds)
            ->where('model_type', User::class)
            ->pluck($modelColumn)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return array<int, int>
     */
    protected function normalizeIds(array $ids): array
    {
        return collect($ids)
            ->map(fn ($value) => is_numeric($value) ? (int) $value : null)
            ->filter(fn ($value) => ! is_null($value) && $value > 0)
            ->unique()
            ->values()
            ->all();
    }
}
