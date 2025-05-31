<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use App\Services\MenuPermissionService;

class PermissionController extends Controller
{
    protected $menuPermissionService;

    public function __construct(MenuPermissionService $menuPermissionService)
    {
        $this->menuPermissionService = $menuPermissionService;
    }

    public function index()
    {
        return Inertia::render('permissions/Index', [
            'roleMenus' => $this->menuPermissionService->getMenusWithPermissions(),
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request, Role $role)
    {
        $permissions = Permission::whereIn('name', $request->input('permissions'))->pluck('id');
        $role->permissions()->sync($permissions);
    }

    public function getMenusWithPermissionsForRole($roleId)
    {
        $role = Role::find($roleId);
        return response()->json($this->menuPermissionService->getMenusWithPermissionsForRole($role));
    }
}
