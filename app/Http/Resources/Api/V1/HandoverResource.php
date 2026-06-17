<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HandoverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'summary' => $this->summary,
            'risk_summary' => $this->risk_summary,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'shift' => $this->whenLoaded('shift', fn () => [
                'id' => $this->shift?->id,
                'name' => $this->shift?->name,
            ]),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'item_type' => $item->item_type,
                'description' => $item->description,
                'priority' => $item->priority,
                'activity_id' => $item->activity_id,
                'incident_id' => $item->incident_id,
                'escalation_id' => $item->escalation_id,
            ])),
            'acknowledgements' => $this->whenLoaded('acknowledgements', fn () => $this->acknowledgements->map(fn ($ack) => [
                'id' => $ack->id,
                'status' => $ack->status,
                'acknowledged_by' => $ack->acknowledgedBy ? [
                    'id' => $ack->acknowledgedBy->id,
                    'name' => $ack->acknowledgedBy->name,
                ] : null,
            ])),
        ];
    }
}
