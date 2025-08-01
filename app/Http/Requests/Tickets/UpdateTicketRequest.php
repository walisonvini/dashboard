<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

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
            'priority' => ['required', 'string', 'in:low,medium,high'],
            'category' => ['required', 'integer', 'exists:ticket_categories,id'],
            'status' => ['required', 'string', 'in:open,in_progress,waiting_user,waiting_third_party,resolved,closed,canceled'],
        ];
    }
}
