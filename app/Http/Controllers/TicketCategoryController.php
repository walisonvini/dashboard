<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TicketCategories\StoreTicketCategoryRequest;
use App\Http\Requests\TicketCategories\UpdateTicketCategoryRequest;

use App\Models\TicketCategory;
use App\Services\TicketCategoryService;

use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;

class TicketCategoryController extends Controller
{
    protected $ticketCategoryService;

    public function __construct(TicketCategoryService $ticketCategoryService)
    {
        $this->ticketCategoryService = $ticketCategoryService;
    }

    public function index(Request $request): Response
    {
        $categories = $this->ticketCategoryService->getPaginatedActiveCategories($request);
        
        return Inertia::render('tickets/categories/Index', [
            'categories' => $categories->items(),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ]
        ]);
    }

    public function store(StoreTicketCategoryRequest $request): RedirectResponse
    {
        TicketCategory::create($request->all());
        return to_route('ticket-categories.index')->with('success', 'Category created successfully');
    }

    public function update(UpdateTicketCategoryRequest $request, TicketCategory $category): RedirectResponse
    {
        $category->update($request->all());
        return to_route('ticket-categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(TicketCategory $category): RedirectResponse
    {
        try {
            $category->delete();
            return to_route('ticket-categories.index')->with('success', 'Category deleted successfully');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return to_route('ticket-categories.index')->with('error', 'Cannot delete this category because it is currently being used by existing tickets. Please deactivate it instead.');
            }
            throw $e;
        }
    }

    public function deactivated(Request $request): Response
    {
        $categories = $this->ticketCategoryService->getPaginatedDeactivatedCategories($request);
        
        return Inertia::render('tickets/categories/Deactivated', [
            'categories' => $categories->items(),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ]
        ]);
    }

    public function deactivate(TicketCategory $category): RedirectResponse
    {
        $category->update(['is_active' => false]);
        return to_route('ticket-categories.index')->with('success', 'Category deactivated successfully');
    }

    public function reactivate(TicketCategory $category): RedirectResponse
    {
        $category->update(['is_active' => true]);
        return to_route('ticket-categories.index')->with('success', 'Category reactivated successfully');
    }
}

