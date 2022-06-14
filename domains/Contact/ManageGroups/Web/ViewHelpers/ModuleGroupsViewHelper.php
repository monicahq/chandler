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
                    'name' => $contact->full_name,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'birthday' => DateHelper::format($contact->birthday),
                    'age' => DateHelper::age($contact->birthday),
                ];
            });

        return [
            'id' => $group->id,
            'name' => $group->name,

            'streaks_statistics' => GoalHelper::getStreakData($group),
            'last_7_days' => self::getLast7Days($group),
            'url' => [
                'update' => route('contact.goal.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'goal' => $group->id,
                ]),
                'streak_update' => route('contact.goal.streak.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'goal' => $group->id,
                ]),
                'destroy' => route('contact.goal.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'goal' => $group->id,
                ]),
            ],
        ];
    }
}
