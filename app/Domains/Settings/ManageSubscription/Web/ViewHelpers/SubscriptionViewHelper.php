<?php

namespace App\Domains\Settings\ManageSubscription\Web\ViewHelpers;

class SubscriptionViewHelper
{
    public static function data(): array
    {
        return [
            'url' => [
                'back' => route('settings.index'),
                'store' => route('settings.subscription.store'),
                'customer_portal' => 'https://customers.monicahq.com',
            ],
        ];
    }
}
