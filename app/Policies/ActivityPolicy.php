<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_activities');
    }

    public function view(User $user, Activity $activity): bool
    {
        return $user->hasPermission('manage_activities')
            || $user->id === $activity->owner_id
            || $user->id === $activity->created_by;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_activities');
    }

    public function update(User $user, Activity $activity): bool
    {
        return $user->hasPermission('update_activities')
            || $user->id === $activity->owner_id;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->hasPermission('manage_activities');
    }
}
