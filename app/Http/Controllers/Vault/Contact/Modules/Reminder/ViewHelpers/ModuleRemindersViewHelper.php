<?php

namespace App\Http\Controllers\Vault\Contact\Modules\Reminder\ViewHelpers;

use App\Models\Note;
use App\Models\User;
use App\Models\Contact;
use App\Helpers\DateHelper;
use Illuminate\Support\Str;

class ModuleRemindersViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $notes = $contact->notes()->orderBy('created_at', 'desc')->take(3)->get();
        $notesCollection = $notes->map(function ($note) use ($contact, $user) {
            return self::dto($contact, $note, $user);
        });
        $emotions = $contact->vault->account->emotions()->get();
        $emotionsCollection = $emotions->map(function ($emotion) {
            return [
                'id' => $emotion->id,
                'name' => $emotion->name,
                'type' => $emotion->type,
            ];
        });

        return [
            'notes' => $notesCollection,
            'emotions' => $emotionsCollection,
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

    public static function dto(Contact $contact, Note $note, User $user): array
    {
        return [
            'id' => $note->id,
            'body' => $note->body,
            'body_excerpt' => Str::length($note->body) >= 200 ? Str::limit($note->body, 200) : null,
            'show_full_content' => false,
            'title' => $note->title,
            'emotion' => $note->emotion ? [
                'id' => $note->emotion->id,
                'name' => $note->emotion->name,
            ] : null,
            'author' => $note->author ? $note->author->name : $note->author_name,
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
