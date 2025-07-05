<?php

namespace App\Services;

use App\Models\User;

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
}