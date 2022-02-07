<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Contact;
use Carbon\Carbon;

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
        if (!$contact->born_at) {
            return null;
        }

        // case: full date
        if (strlen($contact->born_at) == 10) {
            $age = Carbon::parse($contact->born_at)->age;
        }

        // case: only know the age. In this case, we have stored a year.
        if (strlen($contact->born_at) == 4) {
            $age = Carbon::createFromFormat('Y', $contact->born_at)->age;
        }

        // case: only know the month and day.
        if (strlen($contact->born_at) == 5) {
            return null;
        }

        return $age;
    }
}
