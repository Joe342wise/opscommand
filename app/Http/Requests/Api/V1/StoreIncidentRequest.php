<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_incidents') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'severity' => ['required', 'in:P1,P2,P3,P4'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'owner_id' => ['required', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:services,id'],
        ];
    }
}
