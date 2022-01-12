<?php

namespace App\Http\Controllers\Vault\Contact\ViewHelpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ContactIndexViewHelper
{
    public static function data(Collection $contacts, User $user): array
    {
        $contactCollection = collect();
        foreach ($contacts as $contact) {
            $contactCollection->push([
                'id' => $contact->id,
                'name' => $contact->getName($user),
            ]);
        }

        return [
            'contacts' => $contactCollection,
            'url' => [
                'vault' => [
                    'create' => route('vault.create'),
                ],
            ],
        ];
    }
}
