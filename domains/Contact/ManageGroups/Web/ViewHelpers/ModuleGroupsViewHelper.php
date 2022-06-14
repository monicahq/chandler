<?php

namespace App\Contact\ManageGroups\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Helpers\GoalHelper;
use App\Models\Contact;
use App\Models\Group;
use App\Models\User;

class ModuleGroupsViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $groups = $contact->groups()->with('contacts')->orderBy('name')->get();

        $groupsCollection = $groups->map(function ($group) use ($contact) {
            return self::dto($contact, $group);
        });

        return [
            'groups' => $groupsCollection,
            'url' => [
                'store' => route('contact.goal.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, Group $group): array
    {
        $contacts = $group->contacts()
            ->orderBy('first_name')
            ->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'avatar' => $contact->avatar,
                    'url' => [
                        'show' => route('contact.show', [
                            'vault' => $contact->vault_id,
                            'contact' => $contact->id,
                        ]),
                    ],
                ];
            });

        return [
            'id' => $group->id,
            'name' => $group->name,
            'contacts' => $contacts,
            'url' => [
                'update' => route('contact.group.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'goal' => $group->id,
                ]),
                'destroy' => route('contact.group.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'goal' => $group->id,
                ]),
            ],
        ];
    }
}
