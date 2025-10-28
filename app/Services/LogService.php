<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LogService
{
    public function get(): Collection
    {
        return Log::with(['user:id,name,email'])->orderBy('created_at', 'desc')->get();
    }
}
