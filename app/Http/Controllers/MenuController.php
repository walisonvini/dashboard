<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return Inertia::render('menus/Index', [
            'menus' => $menus
        ]);
    }
}
