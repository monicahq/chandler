<?php

namespace App\Contact\ManageContactFeed\Web\ViewHelpers\Actions;

use App\Helpers\MapHelper;
use App\Models\ContactFeedItem;
use App\Models\User;

class ActionFeedAddressCreatedOrUpdated
{
    public static function data(ContactFeedItem $item, User $user): array
    {
        $contact = $item->contact;
        $address = $item->feedable;

        return [
            'address' => [
                'id' => $address->id,
                'street' => $address->street,
                'city' => $address->city,
                'province' => $address->province,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
                'type' => $address->addressType ? [
                    'id' => $address->addressType->id,
                    'name' => $address->addressType->name,
                ] : null,
                'image' => MapHelper::getStaticImage($address, 300, 100),
                'url' => [
                    'show' => MapHelper::getMapLink($address, $user),
                ],
            ],
            'contact' => [
                'id' => $contact->id,
                'name' => $contact->name,
                'age' => $contact->age,
                'avatar' => $contact->avatar,
                'url' => route('contact.show', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }
}
