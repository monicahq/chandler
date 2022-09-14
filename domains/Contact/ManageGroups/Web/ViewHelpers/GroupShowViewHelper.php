<?php

namespace App\Contact\ManageGroups\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GroupShowViewHelper
{
    /**
     * Gets all the contacts in this group.
     * A group has a mandatory type. This type may have one or more roles.
     * Contacts can be assigned to roles, but it's not mandatory.
     * So we need to group contacts by roles if they exist, or list them
     * alphabetically otherwise.
     *
     * @param  Group  $group
     * @param  User  $user
     * @return array
     */
    public static function data(Group $group, User $user): array
    {
        $roles = $group->groupType->groupTypeRoles()->orderBy('position')->get();

        $rolesCollection = collect();
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $contacts = DB::table('contact_group')
                    ->where('group_type_role_id', $role->id)
                    ->where('group_id', $group->id)
                    ->get();

                $contactsCollection = collect();
                foreach ($contacts as $contact) {
                    $contact = Contact::find($contact->contact_id);

                    $contactsCollection->push([
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'age' => $contact->age,
                        'avatar' => $contact->avatar,
                        'url' => route('contact.show', [
                            'vault' => $contact->vault_id,
                            'contact' => $contact->id,
                        ]),
                    ]);
                }

                $rolesCollection->push([
                    'id' => $role->id,
                    'label' => $role->label,
                    'contacts' => $contactsCollection,
                ]);
            }
        }

        // now we get all the contacts that are not assigned to a role
        $contacts = DB::table('contact_group')
            ->whereNull('group_type_role_id')
            ->where('group_id', $group->id)
            ->get();

        $contactsCollection = collect();
        foreach ($contacts as $contact) {
            $contact = Contact::find($contact->contact_id);

            $contactsCollection->push([
                'id' => $contact->id,
                'name' => $contact->name,
                'age' => $contact->age,
                'avatar' => $contact->avatar,
                'url' => route('contact.show', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ]);
        }
        // only adds this row if there is at least one contact
        if ($contactsCollection->count() > 0) {
            $rolesCollection->push([
                'id' => $rolesCollection->count() + 1,
                'label' => 'No role',
                'contacts' => $contactsCollection,
            ]);
        }

        return [
            'id' => $group->id,
            'name' => $group->name,
            'contact_count' => $group->contacts->count(),
            'type' => [
                'label' => $group->groupType->label,
            ],
            'roles' => $rolesCollection,
        ];
    }
}
