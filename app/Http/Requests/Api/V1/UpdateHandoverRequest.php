<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHandoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_handovers') ?? false;
    }

    public function rules(): array
    {
        return [
            'summary' => ['sometimes', 'required', 'string'],
            'risk_summary' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:draft,pending,acknowledged,completed'],
        ];
    }
}
