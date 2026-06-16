<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRemarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyPermission(['manage_activities', 'update_activities']) ?? false;
    }

    public function rules(): array
    {
        return [
            'remark' => ['required', 'string'],
        ];
    }
}
