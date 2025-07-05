<?php

namespace App\Services;

class RoleService
{
    public function veryIfRoleIsModifiable(string $role): bool
    {
        $rolesCanNotBeModified = ['super', 'default'];

        if (in_array($role, $rolesCanNotBeModified)) {
            return false;
        }

        return true;
    }

    public function setDefaultRole($users): void
    {
        foreach ($users as $user) {
            if ($user->roles()->count() === 0) {
                $user->assignRole('default');
            }
        }
    }
}