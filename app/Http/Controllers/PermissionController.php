<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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

    public function index(): Response
    {
        return Inertia::render('permissions/Index', [
            'roleMenus' => $this->menuPermissionService->getMenusWithPermissions(),
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request, Role $role): RedirectResponse
    {
        $permissions = Permission::whereIn('name', $request->input('permissions'))->pluck('id');
        $role->permissions()->sync($permissions);

        app()['cache']->forget('spatie.permission.cache');

        return to_route('permissions.index')->with('success', 'Permissions updated successfully');
    }

    public function getMenusWithPermissionsForRole($roleId): JsonResponse
    {
        $role = Role::find($roleId);
        return response()->json($this->menuPermissionService->getMenusWithPermissionsForRole($role));
    }
}
