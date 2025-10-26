<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Events\LogActionEvent;

class RoleService
{
    public function create(array $data): Role
    {
        $role = Role::create($data);

        event(new LogActionEvent('Role', $role->id, 'created', $role->toArray()));

        return $role;
    }

    public function update(Role $role, array $data): Role
    {
        $oldData = $role->toArray();

        $role->update($data);

        event(new LogActionEvent('Role', $role->id, 'updated', ['old' => $oldData, 'new' => $role->toArray()]));

        return $role;
    }

    public function delete(Role $role): void
    {
        $roleData = $role->toArray();

        $usersWithRole = $role->users;
        
        $role->delete();

        $this->setDefaultRole($usersWithRole);

        event(new LogActionEvent('Role', $role->id, 'deleted', $roleData));
    }

    public function veryIfRoleIsModifiable(string $role): bool
    {
        $rolesCanNotBeModified = ['super', 'default'];

        if (in_array($role, $rolesCanNotBeModified)) {
            return false;
        }

        return true;
    }

    private function setDefaultRole($users): void
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