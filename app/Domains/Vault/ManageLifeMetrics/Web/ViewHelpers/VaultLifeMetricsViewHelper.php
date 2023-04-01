<?php

namespace App\Domains\Vault\ManageLifeMetrics\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\Contact;
use App\Models\LifeMetric;
use App\Models\User;
use App\Models\Vault;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class VaultLifeMetricsViewHelper
{
    public static function data(Vault $vault, User $user, int $year): array
    {
        $lifeMetrics = $vault->lifeMetrics;
        $contact = $user->getContactInVault($vault);

        $lifeMetricsCollection = $lifeMetrics->map(fn (LifeMetric $lifeMetric) => self::dto($lifeMetric, $year, $contact));

        return [
            'data' => $lifeMetricsCollection,
            'url' => [
                'store' => route('vault.life_metrics.store', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }

    public static function dto(LifeMetric $lifeMetric, int $year, Contact $contact): array
    {
        $events = $contact->lifeMetrics()
            ->where('life_metric_id', $lifeMetric->id)
            ->get();

        // get all the events for the given year
        $monthlyEvents = $events->filter(function ($lifeMetricEvent) use ($year) {
            return Carbon::parse($lifeMetricEvent->pivot->created_at)->year == $year;
        });
        $eventsInMonthCollection = collect();
        for ($month = 1; $month < 13; $month++) {
            $eventsCounter = 0;

            foreach ($monthlyEvents as $monthlyEvent) {
                if ((CarbonImmutable::parse($monthlyEvent->pivot->created_at))->month === $month) {
                    $eventsCounter++;
                }
            }
            $date = CarbonImmutable::now()->month($month)->day(1);

            $eventsInMonthCollection->push([
                'month' => $month,
                'friendly_name' => DateHelper::formatMonthNumber($date),
                'events' => $eventsCounter,
            ]);
        }

        return [
            'id' => $lifeMetric->id,
            'incremented' => false,
            'label' => $lifeMetric->label,
            'stats' => self::stats($lifeMetric, $contact),
            'years' => self::years($lifeMetric, $contact),
            'months' => $eventsInMonthCollection,
            'url' => [
                'store' => route('vault.life_metrics.contact.store', [
                    'vault' => $contact->vault->id,
                    'metric' => $lifeMetric->id,
                ]),
                'update' => route('vault.life_metrics.update', [
                    'vault' => $contact->vault->id,
                    'metric' => $lifeMetric->id,
                ]),
                'destroy' => route('vault.life_metrics.destroy', [
                    'vault' => $contact->vault->id,
                    'metric' => $lifeMetric->id,
                ]),
            ],
        ];
    }

    public static function years(LifeMetric $lifeMetric, Contact $contact): Collection
    {
        $events = $contact->lifeMetrics()
            ->where('life_metric_id', $lifeMetric->id)
            ->get();

        $yearsCollection = collect();
        foreach ($events as $lifeMetricEvent) {
            $yearsCollection->push([
                'year' => (Carbon::parse($lifeMetricEvent->pivot->created_at))->year,
            ]);
        }

        return $yearsCollection->unique('year')->sortByDesc('year')->values();
    }

    public static function stats(LifeMetric $lifeMetric, Contact $contact): array
    {
        // get all the events of the current week
        $weeklyEvents = $contact->lifeMetrics()
            ->where('life_metric_id', $lifeMetric->id)
            ->wherePivot('created_at', '<=', CarbonImmutable::now()->endOfWeek())
            ->wherePivot('created_at', '>=', CarbonImmutable::now()->startOfWeek())
            ->count();

        // get all the events of the current month
        $monthlyEvents = $contact->lifeMetrics()
            ->where('life_metric_id', $lifeMetric->id)
            ->wherePivot('created_at', '<=', CarbonImmutable::now()->endOfMonth())
            ->wherePivot('created_at', '>=', CarbonImmutable::now()->startOfMonth())
            ->count();

        // get all the events of the current year
        $yearlyEvents = $contact->lifeMetrics()
            ->where('life_metric_id', $lifeMetric->id)
            ->wherePivot('created_at', '<=', CarbonImmutable::now()->endOfYear())
            ->wherePivot('created_at', '>=', CarbonImmutable::now()->startOfYear())
            ->count();

        return [
            'weekly_events' => $weeklyEvents,
            'monthly_events' => $monthlyEvents,
            'yearly_events' => $yearlyEvents,
        ];
    }
}
