<?php

namespace App\Enums\TicketStatus;

enum TicketPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
