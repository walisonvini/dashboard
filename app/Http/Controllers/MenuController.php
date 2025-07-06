<?php

namespace App\Http\Controllers;

use App\Models\Menu;

use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function index(): Response
    {
        $menus = Menu::all();
        return Inertia::render('menus/Index', [
            'menus' => $menus
        ]);
    }
}
