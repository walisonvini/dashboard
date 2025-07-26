<?php

namespace App\Enums\TicketStatus;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case WaitingUser = 'waiting_user';
    case WaitingThirdParty = 'waiting_third_party';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Canceled = 'canceled';
}
