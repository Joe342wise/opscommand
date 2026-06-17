<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEscalationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('escalate_incidents') ?? false;
    }

    public function rules(): array
    {
        return [
            'target_team_id' => ['sometimes', 'required', 'exists:teams,id'],
            'reason' => ['sometimes', 'required', 'string'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high,critical'],
            'status' => ['sometimes', 'required', 'in:pending,in_progress,resolved,cancelled'],
            'update_summary' => ['nullable', 'string'],
        ];
    }
}
