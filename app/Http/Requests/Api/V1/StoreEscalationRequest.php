<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreEscalationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('escalate_incidents') ?? false;
    }

    public function rules(): array
    {
        return [
            'activity_id' => ['nullable', 'exists:activities,id', 'required_without:incident_id'],
            'incident_id' => ['nullable', 'exists:incidents,id', 'required_without:activity_id'],
            'target_team_id' => ['required', 'exists:teams,id'],
            'reason' => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high,critical'],
        ];
    }
}
