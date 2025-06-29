<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;

use Spatie\Permission\Models\Role;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;

class RoleController extends Controller
{
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
        $role->update($request->all());
        return to_route('roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return to_route('roles.index')->with('success', 'Role deleted successfully');
    }
}
