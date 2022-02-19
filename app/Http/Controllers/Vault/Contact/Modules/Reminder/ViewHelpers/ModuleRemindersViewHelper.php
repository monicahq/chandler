<?php

namespace App\Http\Controllers\Vault\Contact\Modules\Reminder\ViewHelpers;

use App\Helpers\AgeHelper;
use App\Models\Note;
use App\Models\User;
use App\Models\Contact;
use App\Helpers\DateHelper;
use App\Models\ContactReminder;
use Illuminate\Support\Str;

class ModuleRemindersViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $reminders = $contact->reminders()->get();
        $remindersCollection = $reminders->map(function ($reminder) use ($contact, $user) {
            return self::dto($contact, $reminder, $user);
        });

        return [
            'notes' => $remindersCollection,
            'url' => [
                'store' => route('contact.note.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'index' => route('contact.note.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, ContactReminder $reminder, User $user): array
    {
        return [
            'id' => $reminder->id,
            'name',
            'date_to_be_reminded_of',
            'frequency',
            'frequency_number',

            'name' => $reminder->name,
            'date' => AgeHelper::formatDate($reminder->date, $user),

            'url' => [
                'update' => route('contact.note.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'note' => $reminder->id,
                ]),
                'destroy' => route('contact.note.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'note' => $reminder->id,
                ]),
            ],
        ];
    }
}
