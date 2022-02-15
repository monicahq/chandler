<?php

namespace App\Http\Controllers\Vault\Contact\ImportantDates\ViewHelpers;

use App\Models\Note;
use App\Models\User;
use App\Models\Contact;
use App\Helpers\AgeHelper;
use App\Helpers\DateHelper;
use App\Models\ContactDate;
use Illuminate\Support\Str;

class ContactImportantDatesViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $dates = $contact->dates;

        $datesCollection = $dates->map(function ($date) use ($user, $contact) {
            return self::dto($contact, $date, $user);
        });

        return [
            'contact' => [
                'name' => $contact->getName($user),
            ],
            'dates' => $datesCollection,
            'months' => DateHelper::getMonths(),
            'days' => DateHelper::getDays(),
            'url' => [
                'store' => route('contact.date.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'contact' => route('contact.show', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, ContactDate $date, User $user): array
    {
        return [
            'id' => $date->id,
            'label' => $date->label,
            'date' => AgeHelper::formatDate($date->date, $user),
            'type' => $date->type,
            'age' => AgeHelper::getAge($date->date),
            'url' => [
                'update' => route('contact.date.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'date' => $date->id,
                ]),
                'destroy' => route('contact.date.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'date' => $date->id,
                ]),
            ],
        ];
    }
}
