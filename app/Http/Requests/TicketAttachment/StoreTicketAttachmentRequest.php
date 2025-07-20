<?php

namespace App\Http\Requests\TicketAttachment;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketAttachmentRequest extends FormRequest
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
            'files' => 'required|array|min:1|max:5',
            'files.*' => 'file|max:5048'
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Please select at least one file.',
            'files.array' => 'Invalid file format.',
            'files.min' => 'Please select at least one file.',
            'files.max' => 'Maximum 5 files allowed.',
            'files.*.file' => 'Invalid file format.',
            'files.*.max' => 'File size must be less than 5MB.'
        ];
    }
}
