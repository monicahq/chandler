<?php

namespace App\Http\Controllers\Vault\Contact\ViewHelpers;

use App\Models\Contact;
use App\Models\TemplatePage;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ContactShowViewHelper
{
    public static function data(Contact $contact): array
    {
        $templatePages = $contact->template->pages()->orderBy('position', 'asc')->get();

        return [
            'template_pages' => self::getTemplatePagesList($templatePages, $contact),
            'contact_information' => self::getContactInformation($templatePages, $contact),
            'url' => [
            ],
        ];
    }

    private static function getTemplatePagesList(EloquentCollection $templatePages, Contact $contact): Collection
    {
        $templatePages = $templatePages->filter(function ($page) {
            return $page->type != TemplatePage::TYPE_CONTACT;
        });

        $pagesCollection = collect();
        foreach ($templatePages as $page) {
            if ($page->type == TemplatePage::TYPE_CONTACT) {
                continue;
            }

            $pagesCollection->push([
                'id' => $page->id,
                'name' => $page->name,
                'selected' => $page->id === $templatePages->first()->id,
                'url' => [
                    'show' => route('contact.page.show', [
                        'vault' => $contact->vault->id,
                        'contact' => $contact->id,
                        'slug' => $page->slug,
                    ]),
                ],
            ]);
        }

        return $pagesCollection;
    }

    private static function getContactInformation(EloquentCollection $templatePages, Contact $contact): array
    {
        $contactInformationPage = $templatePages->where('type', TemplatePage::TYPE_CONTACT)->first();

        return  [
        ];
    }
}
