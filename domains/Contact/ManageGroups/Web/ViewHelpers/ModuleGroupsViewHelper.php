<?php

namespace App\Contact\ManageGroups\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Helpers\GoalHelper;
use App\Models\Contact;
use App\Models\Goal;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;

class ModuleGroupsViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $groups = $contact->groups()->orderBy('name')->get();

        $groupsCollection = $groups->map(function ($group) use ($contact, $user) {
            return self::dto($contact, $group, $user);
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
        return [
            'id' => $group->id,
            'name' => $group->name,
            'active' => $group->active,
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

    private static function getLast7Days(Goal $group): array
    {
        $streaks = $group->streaks()
            ->whereDate('happened_at', '<=', Carbon::now())
            ->whereDate('happened_at', '>=', Carbon::now()->copy()->subDays(7))
            ->get();

        $last7DaysCollection = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);

            $streak = $streaks->first(function ($streak) use ($date) {
                return $streak->happened_at->format('Y-m-d') === $date->format('Y-m-d');
            });

            $last7DaysCollection->push([
                'id' => $i,
                'day' => DateHelper::formatShortDay($date),
                'day_number' => $date->format('d'),
                'happened_at' => $date->format('Y-m-d'),
                'active' => $streak ? true : false,
            ]);
        }

        return $last7DaysCollection->sortByDesc('id')->values()->all();
    }
}
