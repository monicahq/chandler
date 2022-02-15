<?php

namespace App\Http\Controllers\Vault\Contact\ImportantDates\ViewHelpers;

use App\Models\Note;
use App\Models\User;
use App\Models\Contact;
use App\Helpers\AgeHelper;
use App\Helpers\DateHelper;
use Illuminate\Support\Str;

class ContactImportantDatesViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $dates = $contact->dates;

        $datesCollection = $dates->map(function ($date) use ($user) {
            return [
                'id' => $date->id,
                'label' => $date->label,
                'date' => AgeHelper::formatDate($date->date, $user),
                'type' => $date->type,
                'age' => AgeHelper::getAge($date->date),
            ];
        });

        return [
            'contact' => [
                'name' => $contact->getName($user),
            ],
            'dates' => $datesCollection,
            'url' => [
                'store' => route('contact.note.store', [
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

    public static function dto(Contact $contact, Note $note, User $user): array
    {
        return [
            'id' => $note->id,
            'body' => $note->body,
            'body_excerpt' => Str::length($note->body) >= 200 ? Str::limit($note->body, 200) : null,
            'show_full_content' => false,
            'title' => $note->title,
            'author' => $note->author ? $note->author->name : $note->author_name,
            'emotion' => $note->emotion ? [
                'id' => $note->emotion->id,
                'name' => $note->emotion->name,
            ] : null,
            'written_at' => DateHelper::format($note->created_at, $user),
            'url' => [
                'update' => route('contact.note.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'note' => $note->id,
                ]),
                'destroy' => route('contact.note.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'note' => $note->id,
                ]),
            ],
        ];
    }
}
