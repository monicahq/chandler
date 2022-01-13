<?php

namespace App\Http\Controllers\Vault\Contact\ViewHelpers;

use App\Models\Account;
use App\Models\Contact;
use App\Models\TemplatePage;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class ContactShowViewHelper
{
    public static function data(Contact $contact): array
    {
        $templatePages = $contact->template->pages()->get();

        return [
            'template_pages' => self::getTemplatePagesList($templatePages, $contact),
            'contact_information' => self::getContactInformation($templatePages->first(), $contact),
            'url' => [
            ],
        ];
    }

    private static function getTemplatePagesList(EloquentCollection $templatePages, Contact $contact): Collection
    {
        $pagesCollection = collect();
        foreach ($templatePages as $page) {
            if (!$page->can_be_deleted) {
                continue;
            }

            $pagesCollection->push([
                'id' => $page->id,
                'name' => $page->name,
                'url' => [
                    'show' => route('contact.page.show', [
                        'vault' => $contact->vault->id,
                        'contact' => $contact->id,
                        'page' => $page->id,
                    ]),
                ],
            ]);
        }

        return $pagesCollection;
    }

    private static function getContactInformation(TemplatePage $templatePage, Contact $contact): array
    {
        return  [

        ];
    }
}
