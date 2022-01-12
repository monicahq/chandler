<?php

namespace App\Http\Controllers\Vault\Contact\ViewHelpers;

use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
