<?php

namespace App\Services;

use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketCategoryService
{
    public function getPaginatedActiveCategories(Request $request): LengthAwarePaginator
    {
        $query = TicketCategory::where('is_active', true);
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        return $query->paginate(10);
    }

    public function getPaginatedDeactivatedCategories(Request $request): LengthAwarePaginator
    {
        $query = TicketCategory::where('is_active', false);
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        return $query->paginate(10);
    }
}
