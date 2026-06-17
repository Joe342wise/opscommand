<?php

namespace App\Policies;

use App\Models\Escalation;
use App\Models\User;

class EscalationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('escalate_incidents');
    }

    public function view(User $user, Escalation $escalation): bool
    {
        return $user->hasPermission('escalate_incidents')
            || $user->id === $escalation->owner_id
            || $user->id === $escalation->created_by;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('escalate_incidents');
    }

    public function update(User $user, Escalation $escalation): bool
    {
        return $user->hasPermission('escalate_incidents');
    }

    public function delete(User $user, Escalation $escalation): bool
    {
        return $user->hasPermission('escalate_incidents');
    }
}
