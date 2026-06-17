<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_incidents');
    }

    public function view(User $user, Incident $incident): bool
    {
        return $user->hasPermission('manage_incidents')
            || $user->id === $incident->owner_id
            || $user->id === $incident->created_by;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_incidents');
    }

    public function update(User $user, Incident $incident): bool
    {
        return $user->hasPermission('manage_incidents')
            || $user->id === $incident->owner_id;
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->hasPermission('manage_incidents');
    }
}
