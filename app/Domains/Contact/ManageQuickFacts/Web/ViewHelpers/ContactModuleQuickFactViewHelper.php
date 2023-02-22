<?php

namespace App\Domains\Contact\ManageQuickFacts\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\QuickFact;
use App\Models\Religion;
use App\Models\VaultQuickFactTemplate;
use Illuminate\Support\Collection;

class ContactModuleQuickFactViewHelper
{
    public static function data(Contact $contact, VaultQuickFactTemplate $template): array
    {
        $quickFacts = $contact->quickFacts()
            ->where('vault_quick_facts_template_id', $template->id)
            ->get()
            ->map(fn (QuickFact $quickFact) => self::dto($quickFact));

        return [
            'quick_facts' => $quickFacts,
            'url' => [
                'store' => route('contact.quick_fact.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'template' => $template->id,
                ]),
            ],
        ];
    }

    public static function dto(QuickFact $quickFact): array
    {
        return [
            'id' => $quickFact->id,
            'content' => $quickFact->content,
        ];
    }
}
