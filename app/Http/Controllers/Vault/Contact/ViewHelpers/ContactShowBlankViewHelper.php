<?php

namespace App\Http\Controllers\Vault\Contact\ViewHelpers;

use App\Helpers\NameHelper;
use App\Helpers\VaultHelper;
use App\Models\Contact;
use App\Models\TemplatePage;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;

class ContactShowBlankViewHelper
{
    public static function data(Contact $contact): array
    {
        $templates = $contact->vault->account->templates;

        $templatesCollection = $templates->map(function ($template) use ($contact) {
            return [
                'id' => $template->id,
                'name' => $template->name,
            ];
        });

        return [
            'templates' => $templatesCollection,
            'contact' => [
                'name' => $contact->getName(Auth::user()),
            ],
            'url' => [
                'update' => route('contact.template.update', [
                    'vault' => $contact->vault->id,
                    'contact' => $contact->id,
                ]),
            ]
        ];
    }
}
