<?php

namespace App\Domains\Contact\ManageLifeEvents\Web\ViewHelpers;

use App\Helpers\ContactCardHelper;
use App\Helpers\DateHelper;
use App\Models\Contact;
use App\Models\LifeEvent;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\TimelineEvent;
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
                'load' => route('contact.timeline_event.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'store' => route('contact.timeline_event.store', [
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
            'life_event_types' => $lifeEventCategory->lifeEventTypes()
                ->orderBy('position', 'asc')
                ->get()
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

    public static function timelineEvents($timelineEvents, User $user, Contact $contact): array
    {
        $timelineEventsCollection = $timelineEvents->map(
            fn (TimelineEvent $timelineEvent) => self::dtoTimelineEvent($timelineEvent, $user, $contact)
        );

        return [
            'timeline_events' => $timelineEventsCollection,
        ];
    }

    public static function dtoTimelineEvent(TimelineEvent $timelineEvent, User $user, Contact $contact): array
    {
        return [
            'id' => $timelineEvent->id,
            'label' => $timelineEvent->label,
            'collapsed' => $timelineEvent->collapsed,
            'happened_at' => DateHelper::format($timelineEvent->started_at, $user),
            'life_events' => $timelineEvent->lifeEvents
                ->map(fn (LifeEvent $lifeEvent) => self::dtoLifeEvent($lifeEvent, $user, $contact)),
            'url' => [
                'store' => route('contact.life_event.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'timelineEvent' => $timelineEvent->id,
                ]),
                'toggle' => route('contact.timeline_event.toggle', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'timelineEvent' => $timelineEvent->id,
                ]),
            ],
        ];
    }

    public static function dtoLifeEvent(LifeEvent $lifeEvent, User $user, Contact $contact): array
    {
        return [
            'id' => $lifeEvent->id,
            'emotion_id' => $lifeEvent->emotion_id,
            'collapsed' => $lifeEvent->collapsed,
            'summary' => $lifeEvent->summary,
            'description' => $lifeEvent->description,
            'happened_at' => DateHelper::format($lifeEvent->happened_at, $user),
            'costs' => $lifeEvent->costs,
            'currency_id' => $lifeEvent->currency_id,
            'paid_by_contact_id' => $lifeEvent->paid_by_contact_id,
            'duration_in_minutes' => $lifeEvent->duration_in_minutes,
            'distance_in_km' => $lifeEvent->distance_in_km,
            'from_place' => $lifeEvent->from_place,
            'to_place' => $lifeEvent->to_place,
            'place' => $lifeEvent->place,
            'participants' => $lifeEvent->participants->map(fn (Contact $contact) => ContactCardHelper::data($contact)),
            'timeline_event' => [
                'id' => $lifeEvent->timelineEvent->id,
            ],
            'life_event_type' => [
                'id' => $lifeEvent->lifeEventType->id,
                'label' => $lifeEvent->lifeEventType->label,
                'category' => [
                    'id' => $lifeEvent->lifeEventType->lifeEventCategory->id,
                    'label' => $lifeEvent->lifeEventType->lifeEventCategory->label,
                ],
            ],
            'url' => [
                'toggle' => route('contact.life_event.toggle', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'timelineEvent' => $lifeEvent->timelineEvent->id,
                    'lifeEvent' => $lifeEvent->id,
                ]),
            ],
        ];
    }
}
