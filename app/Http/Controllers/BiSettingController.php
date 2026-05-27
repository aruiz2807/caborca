<?php

namespace App\Http\Controllers;

use App\Models\BiReport;
use App\Models\BiSection;
use App\Models\Role;
use App\Models\User;
use App\Services\BiPermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class BiSettingController extends Controller
{
    public function __construct(
        protected BiPermissionService $biPermissionService
    ) {
    }

    public function index(): Response
    {
        $sections = BiSection::query()
            ->with(['reports' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        $reportPermissionNames = $sections
            ->flatMap(fn (BiSection $section) => $section->reports->map(fn (BiReport $report) => $report->permissionName()))
            ->values()
            ->all();

        $reportPermissionsByName = Permission::query()
            ->whereIn('name', $reportPermissionNames)
            ->where('guard_name', 'web')
            ->with([
                'roles:id,name',
            ])
            ->get()
            ->keyBy('name');

        $userAssignmentsByPermissionId = $this->resolveUserAssignmentsByPermissionId(
            $reportPermissionsByName->pluck('id')->values()->all()
        );

        return Inertia::render('Settings/Bi/Index', [
            'sections' => $sections->map(function (BiSection $section) use ($reportPermissionsByName, $userAssignmentsByPermissionId) {
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'slug' => $section->slug,
                    'permission_name' => $section->permissionName(),
                    'reports' => $section->reports->map(function (BiReport $report) use ($reportPermissionsByName, $userAssignmentsByPermissionId) {
                        $reportPermission = $reportPermissionsByName->get($report->permissionName());
                        $reportUserIds = $reportPermission
                            ? ($userAssignmentsByPermissionId[$reportPermission->id] ?? [])
                            : [];

                        return [
                            'id' => $report->id,
                            'bi_section_id' => $report->bi_section_id,
                            'name' => $report->name,
                            'slug' => $report->slug,
                            'embed_url' => $report->embed_url,
                            'permission_name' => $report->permissionName(),
                            'role_ids' => $reportPermission?->roles?->pluck('id')->values()->all() ?? [],
                            'user_ids' => $reportUserIds,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:bi_sections,name'],
        ]);

        $section = BiSection::query()->create([
            'name' => $validated['name'],
            'slug' => $this->buildUniqueSectionSlug($validated['name']),
        ]);

        $this->biPermissionService->ensureSectionPermission($section);

        return to_route('bi-settings.index')->with('message', 'bi-section-stored');
    }

    public function updateSection(Request $request, BiSection $biSection): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('bi_sections', 'name')->ignore($biSection->id)],
        ]);

        $biSection->forceFill([
            'name' => $validated['name'],
            'slug' => $this->buildUniqueSectionSlug($validated['name'], $biSection->id),
        ])->save();

        return to_route('bi-settings.index')->with('message', 'bi-section-stored');
    }

    public function destroySection(BiSection $biSection): RedirectResponse
    {
        $this->biPermissionService->deleteSection($biSection);

        return to_route('bi-settings.index')->with('message', 'bi-section-deleted');
    }

    public function storeReport(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bi_section_id' => ['required', 'integer', 'exists:bi_sections,id'],
            'name' => ['required', 'string', 'max:255'],
            'embed_url' => ['required', 'string', 'max:2048', 'url'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $report = BiReport::query()->create([
            'bi_section_id' => (int) $validated['bi_section_id'],
            'name' => $validated['name'],
            'slug' => $this->buildUniqueReportSlug((int) $validated['bi_section_id'], $validated['name']),
            'embed_url' => $validated['embed_url'],
        ]);

        $this->biPermissionService->syncReportAccess(
            $report->load('section'),
            $validated['role_ids'] ?? [],
            $validated['user_ids'] ?? []
        );

        return to_route('bi-settings.index')->with('message', 'bi-report-stored');
    }

    public function updateReport(Request $request, BiReport $biReport): RedirectResponse
    {
        $validated = $request->validate([
            'bi_section_id' => ['required', 'integer', 'exists:bi_sections,id'],
            'name' => ['required', 'string', 'max:255'],
            'embed_url' => ['required', 'string', 'max:2048', 'url'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $previousSectionId = $biReport->bi_section_id;

        $biReport->forceFill([
            'bi_section_id' => (int) $validated['bi_section_id'],
            'name' => $validated['name'],
            'slug' => $this->buildUniqueReportSlug((int) $validated['bi_section_id'], $validated['name'], $biReport->id),
            'embed_url' => $validated['embed_url'],
        ])->save();

        $this->biPermissionService->syncReportAccess(
            $biReport->load('section'),
            $validated['role_ids'] ?? [],
            $validated['user_ids'] ?? []
        );

        if ($previousSectionId !== $biReport->bi_section_id) {
            $previousSection = BiSection::query()->find($previousSectionId);
            if ($previousSection) {
                $this->biPermissionService->rebuildSectionPermission($previousSection);
            }
        }

        return to_route('bi-settings.index')->with('message', 'bi-report-stored');
    }

    public function destroyReport(BiReport $biReport): RedirectResponse
    {
        $this->biPermissionService->deleteReport($biReport->load('section'));

        return to_route('bi-settings.index')->with('message', 'bi-report-deleted');
    }

    protected function buildUniqueSectionSlug(string $name, ?int $ignoreId = null): string
    {
        return $this->buildUniqueSlug(
            $name,
            function (string $slug) use ($ignoreId): bool {
                $query = BiSection::query()->where('slug', $slug);

                if ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                }

                return $query->exists();
            }
        );
    }

    protected function buildUniqueReportSlug(int $sectionId, string $name, ?int $ignoreId = null): string
    {
        return $this->buildUniqueSlug(
            $name,
            function (string $slug) use ($sectionId, $ignoreId): bool {
                $query = BiReport::query()
                    ->where('bi_section_id', $sectionId)
                    ->where('slug', $slug);

                if ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                }

                return $query->exists();
            }
        );
    }

    /**
     * @param  callable(string): bool  $slugExists
     */
    protected function buildUniqueSlug(string $name, callable $slugExists): string
    {
        $baseSlug = str($name)->slug()->toString();
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'item';
        $slug = $baseSlug;
        $counter = 2;

        while ($slugExists($slug)) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * @param  array<int, int>  $permissionIds
     * @return array<int, array<int, int>>
     */
    protected function resolveUserAssignmentsByPermissionId(array $permissionIds): array
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
            ->get([$permissionColumn, $modelColumn])
            ->groupBy($permissionColumn)
            ->map(function ($rows) use ($modelColumn) {
                return collect($rows)
                    ->pluck($modelColumn)
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();
            })
            ->all();
    }
}
