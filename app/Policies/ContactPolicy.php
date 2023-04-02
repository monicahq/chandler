<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy extends PolicyBase
{
    /**
     * Determine whether the user can access the model.
     */
    public function any(User $user, $contact, $vault): bool
    {
        if ($contact instanceof Contact) {
            return $contact->vault_id === $this->id($vault);
        }

        return Contact::where([
                'id' => $this->id($contact),
                'vault_id' => $this->id($vault)
            ])->exists();
    }
}
