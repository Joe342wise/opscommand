<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_incidents') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'severity' => ['sometimes', 'required', 'in:P1,P2,P3,P4'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high,critical'],
            'status' => ['sometimes', 'required', 'in:open,in_progress,investigating,resolved,closed'],
            'owner_id' => ['sometimes', 'required', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'update_summary' => ['nullable', 'string'],
        ];
    }
}
