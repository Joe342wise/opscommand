<?php

namespace App\Policies;

use App\Models\Handover;
use App\Models\User;

class HandoverPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_handovers');
    }

    public function view(User $user, Handover $handover): bool
    {
        return $user->hasPermission('manage_handovers')
            || $user->id === $handover->created_by;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_handovers');
    }

    public function update(User $user, Handover $handover): bool
    {
        return $user->hasPermission('manage_handovers')
            || $user->id === $handover->created_by;
    }

    public function delete(User $user, Handover $handover): bool
    {
        return false;
    }
}
