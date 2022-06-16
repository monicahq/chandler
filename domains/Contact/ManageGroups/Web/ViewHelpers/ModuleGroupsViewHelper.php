<?php

namespace App\Contact\ManageGroups\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\Group;

class ModuleGroupsViewHelper
{
    /**
     * All the groups associated with the contact.
     *
     * @param Contact $contact
     * @return array
     */
    public static function data(Contact $contact): array
    {
        $groupsInVault = $contact->vault->groups()->with('contacts')->orderBy('name')->get();
        $groupsInContact = $contact->groups()->with('contacts')->orderBy('name')->get();

        $groupsInVaultCollection = $groupsInVault->map(function ($group) use ($contact, $groupsInContact) {
            $taken = false;
            if ($groupsInContact->contains($group)) {
                $taken = true;
            }

            return self::dto($contact, $group, $taken);
        });

        $groupsInContactCollection = $groupsInContact->map(function ($group) use ($contact) {
            return self::dto($contact, $group);
        });

        return [
            'groups_in_contact' => $groupsInContactCollection,
            'groups_in_vault' => $groupsInVaultCollection,
            'url' => [
                'store' => route('contact.group.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, Group $group, bool $taken = false): array
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
