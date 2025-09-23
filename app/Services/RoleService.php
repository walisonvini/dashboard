<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    public function veryIfRoleIsModifiable(string $role): bool
    {
        $rolesCanNotBeModified = ['super', 'default'];

        if (in_array($role, $rolesCanNotBeModified)) {
            return false;
        }

        return true;
    }

    public function setDefaultRole($users): void
    {
        foreach ($users as $user) {
            if ($user->roles()->count() === 0) {
                $user->assignRole('default');
            }
        }
    }

    public function getPaginatedRoles(Request $request): LengthAwarePaginator
    {
        $query = Role::query();
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            });
        }
        
        return $query->paginate(10);
    }
}