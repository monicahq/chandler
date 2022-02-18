<?php

namespace App\Http\Controllers\Vault\Contact\Modules\Label\ViewHelpers;

use App\Models\Label;
use App\Models\Contact;

class ModuleLabelViewHelper
{
    public static function data(Contact $contact): array
    {
        $labels = $contact->vault->labels;

        $labelsInVaultCollection = $labels->map(function ($label) use ($contact) {
            return self::dtoLabel($label, $contact);
        });

        $labels = $contact->labels;
        $labelsAssociatedWithContactCollection = $labels->map(function ($label) use ($contact) {
            return self::dtoLabel($label, $contact);
        });

        return [
            'labels_in_contact' => $labelsAssociatedWithContactCollection,
            'labels_in_vault' => $labelsInVaultCollection,
            'url' => [
                'update' => route('contact.date.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dtoLabel(Label $label, Contact $contact): array
    {
        return [
            'id' => $label->id,
            'name' => $label->name,
            'bg_color' => $label->bg_color,
            'text_color' => $label->text_color,
            'url' => [
                'update' => route('contact.label.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'label' => $label->id,
                ]),
                'destroy' => route('contact.label.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'label' => $label->id,
                ]),
            ],
        ];
    }
}
