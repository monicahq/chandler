<?php

namespace App\Http\Controllers\Vault\Contact\Modules\Reminder\ViewHelpers;

use App\Helpers\ContactReminderHelper;
use App\Models\User;
use App\Models\Contact;
use App\Models\ContactReminder;
use App\Helpers\ImportantDateHelper;

class ModuleRemindersViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $reminders = $contact->reminders()->get();
        $remindersCollection = $reminders->map(function ($reminder) use ($contact, $user) {
            return self::dto($contact, $reminder, $user);
        });

        return [
            'reminders' => $remindersCollection,
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
            'name' => $reminder->name,
            'date_to_be_reminded_of' => ContactReminderHelper::formatDate($reminder, $user),
            'type' => $reminder->type,
            'frequency_number' => $reminder->frequency_number,
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
