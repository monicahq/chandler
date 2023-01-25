<?php

namespace App\Domains\Contact\ManageLifeEvents\Web\ViewHelpers;

use App\Helpers\ContactCardHelper;
use App\Helpers\DateHelper;
use App\Models\Contact;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\User;
use Carbon\Carbon;

class ModuleLifeEventViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $lifeEventCategoriesCollection = $contact->vault->lifeEventCategories()
            ->with('lifeEventTypes')
            ->orderBy('position', 'asc')
            ->get()
            ->map(fn (LifeEventCategory $lifeEventCategory) => self::dtoLifeEventCategory($lifeEventCategory));

        return [
            'contact' => ContactCardHelper::data($contact),
            'current_date' => Carbon::now($user->timezone)->format('Y-m-d'),
            'current_date_human_format' => DateHelper::format(Carbon::now($user->timezone), $user),
            'life_event_categories' => $lifeEventCategoriesCollection,
            'url' => [
                'store' => route('contact.life_event.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'update' => route('contact.date.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dtoLifeEventCategory(LifeEventCategory $lifeEventCategory): array
    {
        return [
            'id' => $lifeEventCategory->id,
            'label' => $lifeEventCategory->label,
            'life_event_types' => $lifeEventCategory->lifeEventTypes
                ->map(fn (LifeEventType $lifeEventType) => self::dtoLifeEventType($lifeEventType)),
        ];
    }

    public static function dtoLifeEventType(LifeEventType $lifeEventType): array
    {
        return [
            'id' => $lifeEventType->id,
            'label' => $lifeEventType->label,
        ];
    }
}
