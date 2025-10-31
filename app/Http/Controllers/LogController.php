<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\Request;

use App\Models\Log;

use App\Services\LogService;

class LogController extends Controller
{
    public function __construct(
        private LogService $logService
    ){}

    public function index(Request $request): Response
    {
        $logs = $this->logService->getPaginatedLogs($request);

        return Inertia::render('logs/Index', [
                'logs' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ]
            ]
        );
    }

    public function show(Log $log): Response
    {
        return Inertia::render('logs/Show', [
                'log' => $log->load('user:id,name,email'),
            ]
        );
    }
}
