<?php

namespace App\Contact\ManageContactInformation\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Helpers\ImportantDateHelper;
use App\Models\Contact;
use App\Models\ContactInformation;
use App\Models\User;

class ContactInformationViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        $infos = $contact->contactInformation()->with('contactInformationType')->get();

        $infosCollection = $infos->map(function ($info) use ($user, $contact) {
            return self::dto($contact, $info, $user);
        });

        return [
            'contact' => [
                'name' => $contact->name,
            ],
            'dates' => $infosCollection,
            'months' => DateHelper::getMonths(),
            'days' => DateHelper::getDays(),
            'date_types' => $infoTypesCollection,
            'url' => [
                'store' => route('contact.date.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'contact' => route('contact.show', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, ContactInformation $info, User $user): array
    {
        return [
            'id' => $info->id,
            'label' => $info->contactInformation->name,
            'url' => $info->contactInformation->protocol ? $info->contactInformation->protocol . '://' : '',
            'date' => ImportantDateHelper::formatDate($info, $user),
            'type' => $info->contactImportantDateType ? [
                'id' =>  $info->contactImportantDateType->id,
                'label' =>  $info->contactImportantDateType->label,
            ] : null,
            'age' => ImportantDateHelper::getAge($info),
            'choice' => ImportantDateHelper::determineType($info),
            'completeDate' => $completeDate,
            'month' => $info->month,
            'day' => $info->day,
            'url' => [
                'update' => route('contact.date.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'date' => $info->id,
                ]),
                'destroy' => route('contact.date.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'date' => $info->id,
                ]),
            ],
        ];
    }
}
