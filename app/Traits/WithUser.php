<?php

namespace App\Traits;

use App\Models\User;

trait WithUser
{
    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Initialize.
     *
     * @param  User  $user
     * @return self
     */
    public function init(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
