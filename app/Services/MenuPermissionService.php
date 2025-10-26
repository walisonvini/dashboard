<?php

namespace App\Services;

use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Collection;

use App\Events\LogActionEvent;

class MenuPermissionService
{
    public function create(Role $role, $data): void
    {
        $permissions = Permission::whereIn('name', $data->input('permissions'))->pluck('id');
        $role->permissions()->sync($permissions);

        app()['cache']->forget('spatie.permission.cache');

        event(new LogActionEvent('Role', $role->id, 'permissions_updated', [
            'role' => $role->only(['id', 'name']),
            'permissions' => $role->permissions->pluck('name'),
        ]));
    }

    public function getMenusWithPermissionsForRole(Role $role): Collection
    {
        $rolePermissionIds = $role->permissions->pluck('id');

        return Menu::whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->with('permissions:id,name');
                },
                'permissions:id,name'
            ])
            ->get()
            ->map(function ($menu) use ($rolePermissionIds) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'permissions' => $menu->permissions->map(function ($p) use ($rolePermissionIds) {
                        return [
                            'name' => $p->name,
                            'checked' => $rolePermissionIds->contains($p->id),
                        ];
                    }),
                    'children' => $menu->children->map(function ($child) use ($rolePermissionIds) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'permissions' => $child->permissions->map(function ($p) use ($rolePermissionIds) {
                                return [
                                    'name' => $p->name,
                                    'checked' => $rolePermissionIds->contains($p->id),
                                ];
                            }),
                        ];
                    }),
                ];
            });
    }

    public function getMenusWithPermissions(): Collection
    {
        return Menu::whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->with('permissions:id,name');
                },
                'permissions:id,name'
            ])
            ->get();
    }
}
    
