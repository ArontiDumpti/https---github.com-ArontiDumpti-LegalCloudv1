<?php

namespace App\Domain\Identity;

use App\Models\Organization;
use Illuminate\Validation\ValidationException;

class RoleCatalog
{
    /** @return array<string, array{id: string, label: string, permissions: list<string>}> */
    public function all(?Organization $organization = null): array
    {
        $overrides = is_array($organization?->role_permissions) ? $organization->role_permissions : [];
        $roles = [];
        foreach (config('legalcloud.roles', []) as $id => $role) {
            $override = is_array($overrides[$id] ?? null) ? $overrides[$id] : [];
            $roles[$id] = [
                'id' => $id,
                'label' => (string) ($override['label'] ?? $role['label'] ?? $id),
                'permissions' => array_values($override['permissions'] ?? $role['permissions'] ?? []),
            ];
        }

        return $roles;
    }

    /** @return array{id: string, label: string, permissions: list<string>} */
    public function find(string $roleId, ?Organization $organization = null): array
    {
        $role = $this->all($organization)[$roleId] ?? null;
        abort_if($role === null, 404);

        return $role;
    }

    /** @return list<string> */
    public function permissions(): array
    {
        return array_values(config('legalcloud.permissions', []));
    }

    /** @param  list<string>  $permissions */
    public function replacePermissions(Organization $organization, string $roleId, array $permissions): array
    {
        $this->find($roleId, $organization);
        $unknown = array_diff($permissions, $this->permissions());
        if ($unknown !== []) {
            throw ValidationException::withMessages([
                'permisos' => 'Hay permisos que no existen: '.implode(', ', $unknown).'.',
            ]);
        }

        $stored = is_array($organization->role_permissions) ? $organization->role_permissions : [];
        $stored[$roleId] = [
            'label' => $stored[$roleId]['label'] ?? config("legalcloud.roles.{$roleId}.label"),
            'permissions' => array_values(array_unique($permissions)),
        ];
        $organization->forceFill(['role_permissions' => $stored])->save();

        return $this->find($roleId, $organization->refresh());
    }

    public function rename(Organization $organization, string $roleId, string $label): array
    {
        $current = $this->find($roleId, $organization);
        $stored = is_array($organization->role_permissions) ? $organization->role_permissions : [];
        $stored[$roleId] = ['label' => $label, 'permissions' => $current['permissions']];
        $organization->forceFill(['role_permissions' => $stored])->save();

        return $this->find($roleId, $organization->refresh());
    }
}
