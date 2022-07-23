<?php

namespace App\Contact\ManageContactInformation\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\ContactInformation;

class ContactInformationViewHelper
{
    public static function data(Contact $contact): array
    {
        $infos = $contact->contactInformation()->with('contactInformationType')->get();

        $infosCollection = $infos->map(function ($info) use ($contact) {
            return self::dto($contact, $info);
        });

        return [
            'data' => $infosCollection,
            'url' => [
                'store' => route('contact.contact_information.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, ContactInformation $info): array
    {
        return [
            'id' => $info->id,
            'label' => $info->contactInformation->name,
            'data' => $info->contactInformation->protocol ? $info->contactInformation->protocol.$info->data : null,
            'url' => [
                'update' => route('contact.contact_information.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'info' => $info->id,
                ]),
                'destroy' => route('contact.contact_information.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'info' => $info->id,
                ]),
            ],
        ];
    }
}
