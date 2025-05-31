<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'icon', 'route', 'parent_id'];

    public function permissions()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Permission::class, 'menu_permission');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }
}
