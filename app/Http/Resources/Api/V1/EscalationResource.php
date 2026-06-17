<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EscalationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'priority' => $this->priority,
            'status' => $this->status,
            'activity_id' => $this->activity_id,
            'incident_id' => $this->incident_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'target_team' => $this->whenLoaded('targetTeam', fn () => [
                'id' => $this->targetTeam?->id,
                'name' => $this->targetTeam?->name,
            ]),
            'owner' => $this->whenLoaded('owner', fn () => [
                'id' => $this->owner?->id,
                'name' => $this->owner?->name,
                'email' => $this->owner?->email,
            ]),
        ];
    }
}
