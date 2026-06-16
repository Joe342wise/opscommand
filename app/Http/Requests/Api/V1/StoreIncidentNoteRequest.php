<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_incidents') ?? false;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string'],
        ];
    }
}
