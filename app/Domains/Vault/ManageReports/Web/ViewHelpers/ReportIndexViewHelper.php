<?php

namespace App\Domains\Vault\ManageReports\Web\ViewHelpers;

use App\Helpers\ContactCardHelper;
use App\Helpers\DateHelper;
use App\Helpers\ImportantDateHelper;
use App\Models\Contact;
use App\Models\ContactImportantDate;
use App\Models\User;
use App\Models\Vault;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportIndexViewHelper
{
    public static function data(Vault $vault): array
    {
        return [
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
