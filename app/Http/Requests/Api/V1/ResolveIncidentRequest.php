<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ResolveIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_incidents') ?? false;
    }

    public function rules(): array
    {
        return [
            'summary' => ['required', 'string'],
            'root_cause' => ['nullable', 'string'],
            'corrective_action' => ['nullable', 'string'],
            'preventive_action' => ['nullable', 'string'],
        ];
    }
}
