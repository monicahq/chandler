<?php

namespace App\Contact\ManageCalls\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\Call;
use App\Models\Contact;
use App\Models\User;

class ModuleCallsViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $calls = $contact->calls()
            ->orderBy('called_at', 'desc')
            ->get();

        $callsCollection = $calls->map(function ($call) use ($contact, $user) {
            return self::dto($contact, $call, $user);
        });

        return [
            'calls' => $callsCollection,
            'url' => [
                'store' => route('contact.call.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, Call $call, User $user): array
    {
        return [
            'id' => $call->id,
            'called_at' => DateHelper::format($call->called_at, $user),
            'duration' => $call->duration,
            'type' => $call->type,
            'answered' => $call->answered,
            'url' => [
                'update' => route('contact.call.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'call' => $call->id,
                ]),
                'destroy' => route('contact.call.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'call' => $call->id,
                ]),
            ],
        ];
    }
}
