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
            'weeks' => self::buildMonth(
                (int) CarbonImmutable::now()->format('m'),
                (int) CarbonImmutable::now()->format('Y')
            ),
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
                        'id' => $currentDay->subDays($day)->day,
                        'date' => $currentDay->subDays($day)->format('d'),
                        'current_day' => false,
                        'is_in_month' => false,
                    ]);
                }
            }

            for ($day = $daysBeforeFirstDay; $day <= 7; $day++) {
                $weekDays->push([
                    'id' => $currentDay->day,
                    'date' => $currentDay->format('d'),
                    'current_day' => $currentDay->isToday() ? true : false,
                    'is_in_month' => $currentDay->month === $firstDayOfMonth->month ? true : false,
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
