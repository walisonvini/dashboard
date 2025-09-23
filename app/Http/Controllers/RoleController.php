<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;

use App\Services\RoleService;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request): Response
    {
        $roles = $this->roleService->getPaginatedRoles($request);
        
        return Inertia::render('roles/Index', [
            'roles' => $roles->items(),
            'pagination' => [
                'current_page' => $roles->currentPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ]
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Role::create($request->all());
        return back()->with('success', 'Role created successfully');
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {

        if (!$this->roleService->veryIfRoleIsModifiable($role->name)) {
            return back()->with('error', 'Role cannot be modified');
        }

        $role->update($request->all());
        return back()->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (!$role->name || !$this->roleService->veryIfRoleIsModifiable($role->name)) {
            return back()->with('error', 'Role cannot be deleted');
        }

        $usersWithRole = $role->users;
        
        $role->delete();

        $this->roleService->setDefaultRole($usersWithRole);

        return back()->with('success', 'Role deleted successfully');
    }
}
