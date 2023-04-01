<?php

namespace App\Policies;

use App\Models\User;

class VaultPolicy extends PolicyBase
{
    /**
     * Determine whether the user can access the model.
     */
    public function any(User $user, $vault): bool
    {
        return $user->vaults()
            ->wherePivotIn('vault_id', [$this->id($vault)])
            !== null;
    }
}
