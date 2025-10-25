<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Models\User;
use Spatie\Permission\Models\Role;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;

use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ){}

    public function index(Request $request): Response
    {
        $users = $this->userService->getPaginatedUsers($request);
        
        return Inertia::render('users/Index', [
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
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
        $this->userService->create($request);

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

        $this->userService->update($user, $request);

        return to_route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (!$this->userService->veryIfUserIsModifiable($user)) {
            return to_route('users.index')->with('error', 'User cannot be deleted');
        }

        $this->userService->delete($user);

        return to_route('users.index')->with('success', 'User deleted successfully');
    }

    public function trashed(Request $request): Response
    {
        $users = $this->userService->getPaginatedTrashedUsers($request);
        
        return Inertia::render('users/Trashed', [
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    public function restore($userId): RedirectResponse
    {
        $this->userService->restore($userId);

        return to_route('users.index')->with('success', 'User restored successfully');
    }
}
