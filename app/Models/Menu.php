<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'icon', 'route', 'parent_id'];

    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'menu_role');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }
}
