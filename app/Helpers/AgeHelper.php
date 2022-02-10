<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\ContactDate;

class AgeHelper
{
    /**
     * Age in Monica is complex because in real life, we don't always know the
     * exact dates of the persons we meet.
     * Therefore, we need some clever ways of storing that in the DB.
     * Right now, age is stored as a string in the database.
     * There are a bunch of possible use cases:
     * - we know the exact date - so the field is a complete date,
     * - we only know the age,
     * - we only know the day and month.
     *
     * @param  Contact  $contact
     * @return string|null
     */
    public static function getAge(Contact $contact): ?string
    {
        $birthdate = $contact->dates()->where('type', ContactDate::TYPE_BIRTHDATE)
            ->first();

        if (! $birthdate) {
            return null;
        }

        // case: full date
        if (strlen($birthdate->date) == 10) {
            $age = Carbon::parse($birthdate->date)->age;
        }

        // case: only know the age. In this case, we have stored a year.
        if (strlen($birthdate->date) == 4) {
            $age = Carbon::createFromFormat('Y', $birthdate->date)->age;
        }

        // case: only know the month and day.
        if (strlen($birthdate->date) == 5) {
            return null;
        }

        return $age;
    }
}
