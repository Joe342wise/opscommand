<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreHandoverItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_handovers') ?? false;
    }

    public function rules(): array
    {
        return [
            'item_type' => ['required', 'in:activity,incident,escalation,manual'],
            'activity_id' => ['nullable', 'exists:activities,id'],
            'incident_id' => ['nullable', 'exists:incidents,id'],
            'escalation_id' => ['nullable', 'exists:escalations,id'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high,critical'],
        ];
    }
}
