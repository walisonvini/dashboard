<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Http\Request;

class LogService
{
    public function getPaginatedLogs(Request $request)
    {
        $logs = Log::with(['user:id,name,email'])
        ->when($request->filled('actor'), function ($query) use ($request) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->actor}%");
            });
        })
        ->when($request->filled('ip'), function ($query) use ($request) {
            $query->where('ip_address', 'like', "%{$request->ip}%");
        })
        ->when($request->filled('action'), function ($query) use ($request) {
            $query->where('action', 'like', "%{$request->action}%");
        })
        ->when($request->filled('model'), function ($query) use ($request) {
            $query->where('model', 'like', "%{$request->model}%");
        })
        ->when($request->filled('model_id'), function ($query) use ($request) {
            $query->where('model_id', $request->model_id);
        })
        ->orderBy('id', 'desc')
        ->paginate(15);

        return $logs;
    }
}
