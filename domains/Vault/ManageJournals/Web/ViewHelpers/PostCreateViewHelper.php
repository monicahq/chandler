<?php

namespace App\Vault\ManageJournals\Web\ViewHelpers;

use App\Models\Journal;
use App\Models\PostTemplate;
use App\Models\PostTemplateSection;

class PostCreateViewHelper
{
    public static function template(Journal $journal): array
    {
        $templatesCollection = $journal->vault->account->postTemplates()
            ->orderBy('position')
            ->get()
            ->map(fn (PostTemplate $postTemplate) => [
                'id' => $postTemplate->id,
                'label' => $postTemplate->label,
                'sections' => $postTemplate
                    ->postTemplateSections()
                    ->orderBy('position')
                    ->get()
                    ->map(fn (PostTemplateSection $postTemplateSection) => [
                        'id' => $postTemplateSection->id,
                        'label' => $postTemplateSection->label,
                    ]),
                'url' => [
                    'create' => route('post.create', [
                        'vault' => $journal->vault_id,
                        'journal' => $journal->id,
                        'template' => $postTemplate->id,
                    ]),
                ],
            ]);

        return [
            'journal' => [
                'name' => $journal->name,
            ],
            'templates' => $templatesCollection,
            'url' => [
                'back' => route('journal.show', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                ]),
            ],
        ];
    }

    public static function data(Journal $journal, PostTemplate $postTemplate): array
    {
        $sectionsCollection = $postTemplate->postTemplateSections()
            ->orderBy('position')
            ->get()
            ->map(fn (PostTemplateSection $postTemplateSection) => [
                'id' => $postTemplateSection->id,
                'label' => $postTemplateSection->label,
            ]);

        return [
            'journal' => [
                'name' => $journal->name,
            ],
            'sections' => $sectionsCollection,
            'url' => [
                'store' => route('post.store', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                ]),
                'back' => route('journal.show', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                ]),
            ],
        ];
    }
}
