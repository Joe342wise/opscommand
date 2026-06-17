<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationRecipientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'notification' => $this->whenLoaded('notification', fn () => [
                'id' => $this->notification->id,
                'title' => $this->notification->title,
                'message' => $this->notification->message,
                'category' => $this->notification->category,
                'entity_type' => $this->notification->entity_type,
                'entity_id' => $this->notification->entity_id,
                'created_at' => $this->notification->created_at?->toISOString(),
                'alert' => $this->notification->relationLoaded('alert') && $this->notification->alert ? [
                    'id' => $this->notification->alert->id,
                    'title' => $this->notification->alert->title,
                    'severity' => $this->notification->alert->severity,
                    'status' => $this->notification->alert->status,
                ] : null,
            ]),
        ];
    }
}
