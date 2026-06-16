<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyPermission(['manage_activities', 'update_activities']) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high,critical'],
            'status' => ['sometimes', 'required', 'in:pending,in_progress,escalated,completed,cancelled'],
            'owner_id' => ['sometimes', 'required', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
            'update_summary' => ['nullable', 'string'],
        ];
    }
}
