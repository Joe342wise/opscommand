<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreHandoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_handovers') ?? false;
    }

    public function rules(): array
    {
        return [
            'shift_id' => ['required', 'exists:shifts,id'],
            'summary' => ['required', 'string'],
            'risk_summary' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,pending,acknowledged,completed'],
            'items' => ['nullable', 'array'],
            'items.*.item_type' => ['required_with:items', 'in:activity,incident,escalation,manual'],
            'items.*.activity_id' => ['nullable', 'exists:activities,id'],
            'items.*.incident_id' => ['nullable', 'exists:incidents,id'],
            'items.*.escalation_id' => ['nullable', 'exists:escalations,id'],
            'items.*.description' => ['required_with:items', 'string'],
            'items.*.priority' => ['required_with:items', 'in:low,medium,high,critical'],
        ];
    }
}
