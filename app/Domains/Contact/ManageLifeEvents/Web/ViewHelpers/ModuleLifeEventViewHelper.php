<?php

namespace App\Domains\Contact\ManageLifeEvents\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\Label;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;

class ModuleLifeEventViewHelper
{
    public static function data(Contact $contact): array
    {
        $lifeEventCategoriesCollection = $contact->vault->lifeEventCategories()
            ->with('lifeEventTypes')
            ->orderBy('position', 'asc')
            ->get()
            ->map(fn (LifeEventCategory $lifeEventCategory) => self::dtoLifeEventCategory($lifeEventCategory));

        return [
            'life_event_categories' => $lifeEventCategoriesCollection,
            'url' => [
                'store' => route('contact.label.store', [
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
