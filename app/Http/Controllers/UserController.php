<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;

use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\RedirectResponse;

use App\Models\User;
use Spatie\Permission\Models\Role;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): Response
    {
        $users = User::all();
        return Inertia::render('users/Index', [
            'users' => $users
        ]);
    }

    public function create(): Response
    {
        $roles = Role::all();
        return Inertia::render('users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->roles);

        app()['cache']->forget('spatie.permission.cache');

        return to_route('users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user): Response|RedirectResponse
    {
        if (!$this->userService->veryIfUserIsModifiable($user)) {
            return to_route('users.index')->with('error', 'User cannot be modified');
        }

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

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if (!$this->userService->veryIfUserIsModifiable($user)) {
            return to_route('users.index')->with('error', 'User cannot be modified');
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);
        $user->syncRoles($request->roles);

        return to_route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (!$this->userService->veryIfUserIsModifiable($user)) {
            return to_route('users.index')->with('error', 'User cannot be deleted');
        }

        $user->delete();

        return to_route('users.index')->with('success', 'User deleted successfully');
    }

    public function trashed(): Response
    {
        $users = User::onlyTrashed()->get();
        return Inertia::render('users/Trashed', [
            'users' => $users
        ]);
    }

    public function restore($userId): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore();

        return to_route('users.index')->with('success', 'User restored successfully');
    }
}
