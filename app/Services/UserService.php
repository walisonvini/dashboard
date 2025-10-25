<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Events\LogActionEvent;

class UserService
{
    public function create($data): User
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $user->assignRole($data->roles);

        app()['cache']->forget('spatie.permission.cache');

        event(new LogActionEvent('User', $user->id, 'created', $user->toArray()));

        return $user;
    }

    public function update(User $user, $data): User
    {
        $oldData = [
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => $user->getRoleNames(),
        ];

        $user->fill([
            'name' => $data->name,
            'email' => $data->email,
            'password' => !empty($data->password) ? Hash::make($data->password) : $user->password,
        ])->save();

        if (!empty($data->roles)) {
            $user->syncRoles($data->roles);
            app('cache')->forget('spatie.permission.cache');
        }

        $user->fresh();

        $newData = [
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => $user->getRoleNames(),
        ];

        event(new LogActionEvent('User', $user->id, 'updated', ['old' => $oldData, 'new' => $newData]));

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();

        event(new LogActionEvent('User', $user->id, 'deleted'));
    }

    public function restore($userId): void
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore();

        event(new LogActionEvent('User', $user->id, 'restored'));
    }

    public function veryIfUserIsModifiable(User $user): bool
    {
        $usersCanNotBeModified = ['super'];

        if (in_array($user->name, $usersCanNotBeModified)) {
            return false;
        }

        return true;
    }

    public function getPaginatedUsers(Request $request): LengthAwarePaginator
    {
        $query = User::query();
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        
        return $query->paginate(10);
    }

    public function getPaginatedTrashedUsers(Request $request): LengthAwarePaginator
    {
        $query = User::onlyTrashed();
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        
        return $query->paginate(10);
    }
}