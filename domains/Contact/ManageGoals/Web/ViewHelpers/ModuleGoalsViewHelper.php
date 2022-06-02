<?php

namespace App\Contact\ManageGoals\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\Goal;
use App\Models\User;

class ModuleGoalsViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $goals = $contact->goals()->get();
        $activeGoals = $goals->filter(function ($goal) {
            return $goal->active;
        });
        $inactiveGoals = $goals->filter(function ($goal) {
            return ! $goal->active;
        });

        $goalsCollection = $activeGoals->map(function ($goal) use ($contact, $user) {
            return self::dto($contact, $goal, $user);
        });

        return [
            'active_goals' => $goalsCollection,
            'inactive_goals_count' => $inactiveGoals->count(),
            'url' => [
                'store' => route('contact.goal.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, Goal $goal): array
    {
        return [
            'id' => $goal->id,
            'name' => $goal->name,
            'active' => $goal->active,
            'url' => [
                'update' => route('contact.goal.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'goal' => $goal->id,
                ]),
                'destroy' => route('contact.goal.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'goal' => $goal->id,
                ]),
            ],
        ];
    }
}
