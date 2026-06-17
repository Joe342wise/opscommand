<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceMetricRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_services') ?? false;
    }

    public function rules(): array
    {
        return [
            'metric_name' => ['required', 'string', 'max:255'],
            'metric_value' => ['required', 'numeric'],
            'unit' => ['nullable', 'string', 'max:50'],
        ];
    }
}
