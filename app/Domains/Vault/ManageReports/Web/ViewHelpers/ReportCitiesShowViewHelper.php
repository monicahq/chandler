<?php

namespace App\Domains\Vault\ManageReports\Web\ViewHelpers;

use App\Helpers\ContactCardHelper;
use App\Helpers\MapHelper;
use App\Models\Contact;
use App\Models\Vault;
use Illuminate\Support\Str;

class ReportCitiesShowViewHelper
{
    public static function data(Vault $vault, string $city): array
    {
        $addresses = $vault->addresses()
            ->select('id', 'city')
            ->whereNotNull('city')
            ->where('city', Str::ucfirst($city))
            ->where('city', Str::lcfirst($city))
            ->with('contacts')
            ->get()
            ->map(fn ($address) => [
                'id' => $address->id,
                'name' => Str::ucfirst($address->city),
                'address' => MapHelper::getAddressAsString($address),
                'contacts' => $address->contacts()
                    ->get()
                    ->map(fn (Contact $contact) => ContactCardHelper::data($contact)),
            ])
            ->unique('name');

        return [
            'city' => Str::ucfirst($city),
            'addresses' => $addresses,
            'url' => [
                'mood_tracking_events' => route('vault.reports.mood_tracking_events.index', [
                    'vault' => $vault->id,
                ]),
                'important_date_summary' => route('vault.reports.important_dates.index', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }
}
