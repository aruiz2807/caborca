<?php

namespace App\Services;

class PermissionCompatibilityService
{
    /**
     * Resolve additional permissions that should be granted from compatibility rules.
     *
     * @param  array<int, string>  $assignedPermissions
     * @param  array<int|string, string|bool>  $knownPermissions
     * @return array{grants: array<int, string>, missing: array<int, string>}
     */
    public function resolve(array $assignedPermissions, array $knownPermissions = []): array
    {
        $assigned = $this->normalizePermissions($assignedPermissions);
        $assignedLookup = array_fill_keys($assigned, true);
        $knownLookup = $this->normalizeKnownPermissions($knownPermissions);
        $hasKnownPermissionsConstraint = ! empty($knownLookup);

        $grants = [];
        $missing = [];

        foreach ($this->rules() as $rule) {
            $ifAny = $this->normalizePermissions($rule['if_any'] ?? []);
            $grant = $this->normalizePermissions($rule['grant'] ?? []);

            if (empty($ifAny) || empty($grant)) {
                continue;
            }

            $hasAnyTrigger = false;

            foreach ($ifAny as $permissionName) {
                if (isset($assignedLookup[$permissionName])) {
                    $hasAnyTrigger = true;
                    break;
                }
            }

            if (! $hasAnyTrigger) {
                continue;
            }

            foreach ($grant as $permissionName) {
                if ($hasKnownPermissionsConstraint && ! isset($knownLookup[$permissionName])) {
                    $missing[$permissionName] = true;
                    continue;
                }

                if (isset($assignedLookup[$permissionName])) {
                    continue;
                }

                $assignedLookup[$permissionName] = true;
                $grants[$permissionName] = true;
            }
        }

        return [
            'grants' => array_keys($grants),
            'missing' => array_keys($missing),
        ];
    }

    /**
     * Expand assigned permissions with compatible grants.
     *
     * @param  array<int, string>  $assignedPermissions
     * @param  array<int|string, string|bool>  $knownPermissions
     * @return array<int, string>
     */
    public function expand(array $assignedPermissions, array $knownPermissions = []): array
    {
        $assigned = $this->normalizePermissions($assignedPermissions);
        $resolved = $this->resolve($assigned, $knownPermissions);

        return array_values(array_unique(array_merge($assigned, $resolved['grants'])));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function rules(): array
    {
        $rules = config('permission_compatibility_map.rules', []);

        return is_array($rules) ? $rules : [];
    }

    /**
     * @param  mixed  $permissions
     * @return array<int, string>
     */
    protected function normalizePermissions(mixed $permissions): array
    {
        if (! is_array($permissions)) {
            return [];
        }

        return array_values(array_unique(array_filter($permissions, function ($permissionName) {
            return is_string($permissionName) && $permissionName !== '';
        })));
    }

    /**
     * @param  array<int|string, string|bool>  $knownPermissions
     * @return array<string, bool>
     */
    protected function normalizeKnownPermissions(array $knownPermissions): array
    {
        if (empty($knownPermissions)) {
            return [];
        }

        $firstKey = array_key_first($knownPermissions);
        if ($firstKey !== null && ! is_int($firstKey)) {
            $lookup = [];

            foreach ($knownPermissions as $permissionName => $enabled) {
                if (is_string($permissionName) && $permissionName !== '' && (bool) $enabled) {
                    $lookup[$permissionName] = true;
                }
            }

            return $lookup;
        }

        return array_fill_keys($this->normalizePermissions($knownPermissions), true);
    }
}
