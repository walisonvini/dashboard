<?php

namespace App\Services;

use App\Models\Menu;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Collection;

class MenuPermissionService
{
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

    public function getMenusWithPermissions()
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
    
