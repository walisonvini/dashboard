<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = ['name', 'icon', 'route', 'parent_id'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Permission::class, 'menu_permission');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }
}
