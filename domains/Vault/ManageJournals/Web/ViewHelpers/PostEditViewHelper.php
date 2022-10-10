<?php

namespace App\Vault\ManageJournals\Web\ViewHelpers;

use App\Helpers\PostHelper;
use App\Models\Contact;
use App\Models\Journal;
use App\Models\Post;
use App\Models\PostSection;

class PostEditViewHelper
{
    public static function data(Journal $journal, Post $post): array
    {
        $sectionsCollection = $post->postSections()
            ->orderBy('position')
            ->get()
            ->map(fn (PostSection $postSection) => [
                'id' => $postSection->id,
                'label' => $postSection->label,
                'content' => $postSection->content,
            ]);

        $contactsCollection = $post->contacts()
            ->get()
            ->map(fn (Contact $contact) => [
                'id' => $contact->id,
                'name' => $contact->name,
                'url' => route('contact.show', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ]);

        return [
            'id' => $post->id,
            'title' => $post->title,
            'sections' => $sectionsCollection,
            'statistics' => PostHelper::statistics($post),
            'contacts' => $contactsCollection,
            'journal' => [
                'name' => $journal->name,
            ],
            'url' => [
                'update' => route('post.update', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                    'post' => $post->id,
                ]),
                'show' => route('post.show', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                    'post' => $post->id,
                ]),
                'back' => route('journal.show', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                ]),
                'destroy' => route('post.destroy', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                    'post' => $post->id,
                ]),
            ],
        ];
    }
}
