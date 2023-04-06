<?php

namespace App\Domains\Vault\ManageCalendar\Web\ViewHelpers;

use App\Helpers\ContactCardHelper;
use App\Helpers\DateHelper;
use App\Models\ContactImportantDate;
use App\Models\MoodTrackingEvent;
use App\Models\User;
use App\Models\Vault;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VaultCalendarIndexViewHelper
{
    public static function data(Vault $vault, User $user, int $year, int $month): array
    {
        $date = Carbon::createFromDate($year, $month, 1);
        $previousMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();

        $collection = self::buildMonth($vault, Auth::user(), $month, $year);

        return [
            'month' => $month,
            'year' => $year,
            'current_month' => DateHelper::formatLongMonthAndYear($date),
            'weeks' => $collection,
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

    public static function buildMonth(Vault $vault, User $user, int $month, int $year): Collection
    {
        $month = Str::padLeft($month, 2, '0');
        $firstDayOfMonth = CarbonImmutable::createFromDate($year, $month, 1)->startOfMonth();
        $lastDayOfMonth = CarbonImmutable::createFromDate($year, $month, 1)->endOfMonth();
        $numberOfWeeksInMonth = DateHelper::weeksInMonth($firstDayOfMonth);

        $calendarWeeks = collect();
        $currentDay = $firstDayOfMonth;

        $contactsId = $vault->contacts()->pluck('id');

        for ($week = 1; $week <= $numberOfWeeksInMonth; $week++) {
            // for the first week, we need to add the days before the first day
            // of the month
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

            // then we loop over the days of the months
            for ($day = $daysBeforeFirstDay; $day <= 7; $day++) {
                $weekDays->push([
                    'id' => $currentDay->day,
                    'date' => $currentDay->format('d'),
                    'current_day' => $currentDay->isToday() ? true : false,
                    'is_in_month' => $currentDay->month === $firstDayOfMonth->month ? true : false,
                    'important_dates' => self::getImportantDates($currentDay->day, $currentDay->month, $contactsId),
                    'mood_events' => self::getMood($vault, $user, $currentDay),
                    'url' => [
                        'show' => route('vault.calendar.day', [
                            'vault' => $vault->id,
                            'year' => $currentDay->year,
                            'month' => $currentDay->month,
                            'day' => $currentDay->day,
                        ]),
                    ],
                ]);
                $currentDay = $currentDay->addDay();
            }

            $calendarWeeks->push([
                'id' => $week,
                'days' => $weekDays,
            ]);
        }

        // one more row representing a week might be missing, depending on the
        // week
        if ($currentDay->isBefore($lastDayOfMonth)) {
            $weekDays = collect();
            for ($day = $currentDay->dayOfWeekIso; $day <= 7; $day++) {
                $weekDays->push([
                    'id' => $currentDay->day,
                    'date' => $currentDay->format('d'),
                    'current_day' => $currentDay->isToday() ? true : false,
                    'is_in_month' => $currentDay->month === $firstDayOfMonth->month ? true : false,
                    'important_dates' => self::getImportantDates($currentDay->day, $currentDay->month, $contactsId),
                    'mood_events' => self::getMood($vault, $user, $currentDay),
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

    public static function getImportantDates(int $day, int $month, Collection $contactsId): Collection
    {
        return ContactImportantDate::where('day', $day)
            ->where('month', $month)
            ->whereIn('contact_id', $contactsId)
            ->with('contact')
            ->get()
            ->unique('contact_id')
            ->map(fn (ContactImportantDate $importantDate) => ContactCardHelper::data($importantDate->contact));
    }

    public static function getMood(Vault $vault, User $user, CarbonImmutable $date): Collection
    {
        $contact = $user->getContactInVault($vault);

        return $contact->moodTrackingEvents()
            ->with('moodTrackingParameter')
            ->whereDate('rated_at', $date)
            ->get()
            ->map(fn (MoodTrackingEvent $moodTrackingEvent) => [
                'id' => $moodTrackingEvent->id,
                'mood' => $moodTrackingEvent->mood,
                'mood_tracking_parameter' => [
                    'id' => $moodTrackingEvent->moodTrackingParameter->id,
                    'label' => $moodTrackingEvent->moodTrackingParameter->label,
                    'hex_color' => $moodTrackingEvent->moodTrackingParameter->hex_color,
                ],
            ]);
    }

    public static function getDay(Vault $vault, int $day, int $month, int $year): array
    {
        $date = Carbon::createFromDate($year, $month, $day);
        $contactsId = $vault->contacts()->pluck('id');
        $importantDates = ContactImportantDate::where('day', $day)
            ->where('month', $month)
            ->whereIn('contact_id', $contactsId)
            ->with('contact')
            ->get()
            ->map(fn (ContactImportantDate $importantDate) => [
                'id' => $importantDate->id,
                'label' => $importantDate->label,
                'type' => [
                    'id' => $importantDate->contactImportantDateType?->id,
                    'label' => $importantDate->contactImportantDateType?->label,
                ],
                'contact' => ContactCardHelper::data($importantDate->contact),
            ]);

        return [
            'day' => DateHelper::formatFullDate($date),
            'important_dates' => $importantDates,
        ];
    }
}
