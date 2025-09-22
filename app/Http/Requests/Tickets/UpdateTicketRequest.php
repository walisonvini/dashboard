<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


use App\Enums\TicketStatus\TicketPriority;
use App\Enums\TicketStatus\TicketStatus;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'priority' => ['required', Rule::Enum(TicketPriority::class)],
            'category_id' => ['required', 'integer', Rule::exists('ticket_categories', 'id')],
            'status' => ['required', Rule::enum(TicketStatus::class)],
        ];
    }
}
