<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TicketCategory;

use Inertia\Inertia;
use Inertia\Response;

class TicketCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = TicketCategory::all();
        return Inertia::render('tickets/categories/Index', [
            'categories' => $categories,
        ]);
    }
}
