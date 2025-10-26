<?php

namespace App\Services;

use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Events\LogActionEvent;

class TicketCategoryService
{
    public function create($data): TicketCategory
    {
        $category = TicketCategory::create($data);

        dd($category);

        event(new LogActionEvent('TicketCategory', $category->id, 'created', $category->toArray()));

        return $category;
    }

    public function update(TicketCategory $category, $data): TicketCategory
    {
        $oldData = $category->toArray();

        $category->update($data);

        event(new LogActionEvent('TicketCategory', $category->id, 'updated', ['old' => $oldData, 'new' => $category->toArray()]));

        return $category;
    }

    public function delete(TicketCategory $category): void
    {
        $catecoryData = $category->toArray();

        $category->delete();

        event(new LogActionEvent('TicketCategory', $category->id, 'deleted', $catecoryData));
    }

    public function deactivate(TicketCategory $category): void
    {
        $category->update(['is_active' => false]);

        event(new LogActionEvent('TicketCategory', $category->id, 'deactivated'));
    }

    public function reactivate(TicketCategory $category): void
    {
        $category->update(['is_active' => true]);

        event(new LogActionEvent('TicketCategory', $category->id, 'reactivated'));
    }

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
