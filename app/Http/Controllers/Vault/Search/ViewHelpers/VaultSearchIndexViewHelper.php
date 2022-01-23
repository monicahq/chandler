<?php

namespace App\Http\Controllers\Vault\Search\ViewHelpers;

use App\Models\Note;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Support\Collection;

class VaultSearchIndexViewHelper
{
    public static function data(Vault $vault, string $term = null): array
    {
        return [
            'contacts' => $term ? self::contacts($vault, $term) : [],
            'notes' => $term ? self::notes($vault, $term) : [],
            'url' => [
                'search' => route('vault.search.show', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }

    public static function contacts(Vault $vault, string $term): Collection
    {
        $contacts = Contact::search($term)
            ->where('vault_id', $vault->id)
            ->get();

        $contactsCollection = $contacts->map(function (Contact $contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->first_name.' '.$contact->last_name.' ('.$contact->nickname.')'.' '.$contact->maiden_name.' '.$contact->middle_name,
                'url' => route('contact.show', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ];
        });

        return $contactsCollection;
    }

    public static function notes(Vault $vault, string $term): Collection
    {
        $notes = Note::search($term)
            ->where('vault_id', $vault->id)
            ->get();

        $notesCollection = $notes->map(function (Note $note) {
            return [
                'id' => $note->id,
                'title' => $note->title,
                'body' => $note->body,
            ];
        });

        return $notesCollection;
    }
}
