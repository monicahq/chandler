<?php

namespace App\Contact\ManageGroups\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GroupShowViewHelper
{
    /**
     * Gets all the contacts in this group.
     * A group has a type. The type may have one or more roles.
     * So we need to group contacts by roles if they exist, or list them
     * alphabetically otherwise.
     *
     * @param  Group  $group
     * @return Collection
     */
    public static function data(Group $group): Collection
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
                    $contact = Contact::find($contact->id);

                    $contactsCollection->push([
                        'name' => $contact->name,
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
            $contact = Contact::find($contact->id);

            $contactsCollection->push([
                'name' => $contact->name,
            ]);
        }
        $rolesCollection->push([
            'id' => $rolesCollection->count() + 1,
            'label' => 'No role',
            'contacts' => $contactsCollection,
        ]);

        return [
            'id' => $group->id,
            'name' => $group->name,
            'type' => [
                'label' => $group->groupType->label,
            ],
            'roles' => $rolesCollection,
        ];
    }
}
