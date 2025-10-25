<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\LogActionEvent;
use App\Models\Log;

class LogActionListener implements ShouldQueue
{
    public $queue = 'logs';

    public function __construct()
    {
        //
    }

    public function handle(LogActionEvent $event): void
    {
        Log::create([
            'user_id' => $event->performedBy,
            'model' => $event->model,
            'model_id' => $event->model_id,
            'action' => $event->action,
            'data' => $event->data,
            'ip_address' => $event->ipAddress,
        ]);
    }
}
