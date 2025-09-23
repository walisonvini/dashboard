<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
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
}