<?php

namespace App\Domains\Contact\Dav\Event;

use App\Models\User\SyncToken;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokenDeleteEvent
{
    use Dispatchable, SerializesModels;

    /**
     * The deleted token.
     *
     * @var SyncToken
     */
    public $token;

    /**
     * Create a new event instance.
     *
     * @param  SyncToken  $token
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
}
