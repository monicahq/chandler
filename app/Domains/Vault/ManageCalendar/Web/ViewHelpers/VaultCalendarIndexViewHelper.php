<?php

namespace App\Domains\Vault\ManageCalendar\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\Vault;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VaultCalendarIndexViewHelper
{
    public static function data(Vault $vault, int $year, int $month): array
    {
        $date = Carbon::createFromDate($year, $month, 1);
        $previousMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();

        return [
            'current_month' => DateHelper::formatLongMonthAndYear($date),
            'weeks' => self::buildMonth($month, $year),
            'previous_month' => DateHelper::formatLongMonthAndYear($previousMonth),
            'next_month' => DateHelper::formatLongMonthAndYear($nextMonth),
            'url' => [
                'previous' => route('vault.calendar.month', [
                    'vault' => $vault->id,
                    'year' => $previousMonth->year,
                    'month' => $previousMonth->month,
                ]),
                'next' => route('vault.calendar.month', [
                    'vault' => $vault->id,
                    'year' => $nextMonth->year,
                    'month' => $nextMonth->month,
                ]),
            ],
        ];
    }

    public static function buildMonth(int $month, int $year): Collection
    {
        $month = Str::padLeft($month, 2, '0');
        $firstDayOfMonth = CarbonImmutable::createFromDate($year, $month, 1)->startOfMonth();
        $lastDayOfMonth = CarbonImmutable::createFromDate($year, $month, 1)->endOfMonth();
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

        // one more row is missing
        if ($currentDay->isBefore($lastDayOfMonth)) {
            $weekDays = collect();
            for ($day = $currentDay->dayOfWeekIso; $day <= 7; $day++) {
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
