<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Spatie\Permission\Models\Role;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;

use App\Services\RoleService;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = Role::all();
        return Inertia::render('roles/Index', [
            'roles' => $roles
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        Role::create($request->all());
        return to_route('roles.index')->with('success', 'Role created successfully');
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {

        if (!$this->roleService->veryIfRoleIsModifiable($role->name)) {
            return to_route('roles.index')->with('error', 'Role cannot be modified');
        }

        $role->update($request->all());
        return to_route('roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        if (!$this->roleService->veryIfRoleIsModifiable($role->name)) {
            return to_route('roles.index')->with('error', 'Role cannot be deleted');
        }

        $usersWithRole = $role->users;
        
        $role->delete();

        $this->roleService->setDefaultRole($usersWithRole);

        return to_route('roles.index')->with('success', 'Role deleted successfully');
    }
}
