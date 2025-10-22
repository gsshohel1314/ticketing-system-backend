<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'     => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|in:technical,billing,others',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:open,in_progress,resolved,closed',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}
