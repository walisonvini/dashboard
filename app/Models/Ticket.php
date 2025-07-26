<?php

namespace App\Models;

use App\Enums\TicketStatus\TicketPriority;
use App\Enums\TicketStatus\TicketStatus;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{

    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'priority', 'category_id',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function isClosedOrCanceled(): bool
    {
        return $this->status === TicketStatus::Closed || $this->status === TicketStatus::Canceled;
    }

    public function isOpen(): bool
    {
        return $this->status === TicketStatus::Open;
    }
}
