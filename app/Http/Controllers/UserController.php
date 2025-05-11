<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Inertia\Inertia;

use App\Models\User;
use Spatie\Permission\Models\Role;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return Inertia::render('users/Index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        return Inertia::render('users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->roles);

        return to_route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return Inertia::render('users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray()
            ],
            'roles' => $roles
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);
        $user->syncRoles($request->roles);

        return to_route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return to_route('users.index')->with('success', 'User deleted successfully.');
    }

    public function restore(User $user)
    {
        $user->restore();

        return to_route('users.index')->with('success', 'User restored successfully.');
    }
}
