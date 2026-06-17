<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlaRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sla_name' => $this->sla_name,
            'target_value' => $this->target_value,
            'actual_value' => $this->actual_value,
            'unit' => $this->unit,
            'is_met' => $this->is_met,
            'period_start' => $this->period_start?->toISOString(),
            'period_end' => $this->period_end?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
