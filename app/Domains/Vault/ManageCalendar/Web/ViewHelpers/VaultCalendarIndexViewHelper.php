<?php

namespace App\Domains\Vault\ManageCalendar\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\Vault;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VaultCalendarIndexViewHelper
{
    public static function data(Vault $vault): array
    {
        return [

        ];
    }

    public static function buildMonth(int $month, int $year): Collection
    {
        $month = Str::padLeft($month, 2, '0');
        $firstDayOfMonth = CarbonImmutable::createFromDate($year, $month, 1)->startOfMonth();
        $numberOfWeeksInMonth = DateHelper::weeksInMonth($firstDayOfMonth);

        $calendarWeeks = collect();
        $currentDay = $firstDayOfMonth;

        for ($week = 1; $week <= $numberOfWeeksInMonth; $week++) {
            // get days in the week
            // for the first week, we need to add the days before the first day
            //of the month
            $weekDays = collect();
            $daysBeforeFirstDay = 1;
            if ($week === 1) {
                for ($day = $currentDay->dayOfWeekIso - 1; $day > 0; $day--) {
                    $daysBeforeFirstDay++;
                    $weekDays->push([
                        'id' => $day,
                        'date' => $currentDay->subDays($day)->format('d'),
                    ]);
                }
            }

            for ($day = $daysBeforeFirstDay; $day <= 7; $day++) {
                $weekDays->push([
                    'id' => $day,
                    'date' => $currentDay->format('d'),
                ]);
                $currentDay = $currentDay->addDay();
            }

            $calendarWeeks->push([
                'id' => $week,
                'days' => $weekDays,
            ]);
        }

        return $calendarWeeks;
    }
}
