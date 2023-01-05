<?php

namespace App\Domains\Contact\ManageLifeEvents\Web\ViewHelpers;

use App\Helpers\ContactCardHelper;
use App\Helpers\DateHelper;
use App\Helpers\MonetaryNumberHelper;
use App\Models\Contact;
use App\Models\LifeEvent;
use App\Models\LifeEventCategory;
use App\Models\User;

class ModuleLifeEventViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $lifeEventsCollection = $contact->lifeEvents()
            ->get()
            ->map(fn (LifeEvent $lifeEvent) => ModuleLifeEventViewHelper::dtoLifeEvent($lifeEvent, $user));

        $lifeEventCategoriesCollection = $user->account
            ->lifeEventCategories()
            ->orderBy('label')
            ->get()
            ->map(fn (LifeEventCategory $category) => [
                'id' => $category->id,
                'label' => $category->label,
            ]);

        return [
            'lifeEvents' => $lifeEventsCollection,

            'url' => [
                'currencies' => route('currencies.index'),
                'store' => route('contact.loan.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dtoLifeEvent(LifeEvent $lifeEvent, User $user): array
    {
        $participantsCollection = $lifeEvent->participants()
            ->map(fn ($participant) => ContactCardHelper::data($participant));

        return [
            'id' => $lifeEvent->id,
            'life_event_type_id' => [
                'id' => $lifeEvent->lifeEventType->id,
                'label' => $lifeEvent->lifeEventType->label,
            ],
            'collapsed' => $lifeEvent->collapsed,
            'summary' => $lifeEvent->summary,
            'description' => $lifeEvent->description,
            'happened_at' => DateHelper::format($lifeEvent->happened_at, $user),
            'costs' => $lifeEvent->costs ? MonetaryNumberHelper::format($user, $lifeEvent->costs) : null,
            'currency' => $lifeEvent->currency ? [
                'id' => $lifeEvent->currency->id,
                'code' => $lifeEvent->currency->code,
            ] : null,
            'paid_by_contact_id' => $lifeEvent->paidBy ?
                ContactCardHelper::data($lifeEvent->paidBy) :
                null,
            'duration_in_minutes' => $lifeEvent->duration_in_minutes,
            'distance_in_km' => $lifeEvent->distance_in_km,
            'from_place' => $lifeEvent->from_place,
            'to_place' => $lifeEvent->to_place,
            'place' => $lifeEvent->place,
            'participants' => $participantsCollection,
            'url' => [],
        ];
    }
}
