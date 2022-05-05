<?php

namespace App\Contact\ManageRelationships\Web\ViewHelpers;

use App\Helpers\AvatarHelper;
use App\Models\Contact;
use App\Models\User;
use App\Models\Vault;

class ContactRelationshipsCreateViewHelper
{
    public static function data(Vault $vault, Contact $contact, User $user): array
    {
        $account = $vault->account;

        $genders = $account->genders()->orderBy('name', 'asc')->get();
        $genderCollection = $genders->map(function ($gender) {
            return [
                'id' => $gender->id,
                'name' => $gender->name,
            ];
        });

        $pronouns = $account->pronouns()->orderBy('name', 'asc')->get();
        $pronounCollection = $pronouns->map(function ($pronoun) {
            return [
                'id' => $pronoun->id,
                'name' => $pronoun->name,
            ];
        });

        $relationshipTypeGroupsCollection = $account->relationshipGroupTypes()
            ->with('types')
            ->get()
            ->map(function ($relationshipTypeGroup) {
                return [
                    'id' => $relationshipTypeGroup->id,
                    'name' => $relationshipTypeGroup->name,
                    'types' => $relationshipTypeGroup->types()->get()->map(function ($relationshipType) {
                        return [
                            'id' => $relationshipType->id,
                            'name' => $relationshipType->name,
                        ];
                    }),
                ];
        });

        return [
            'contact' => [
                'id' => $contact->id,
                'name' => $contact->getName($user),
                'avatar' => AvatarHelper::getSVG($contact),
            ],
            'genders' => $genderCollection,
            'pronouns' => $pronounCollection,
            'relationship_types' => $relationshipTypeGroupsCollection,
            'url' => [
                'store' => route('contact.store', [
                    'vault' => $vault->id,
                ]),
                'back' => route('contact.index', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }
}
