<?php

namespace App\Helpers;

use App\Models\User;

class DistanceHelper
{
    /**
     * Format the distance according to the preferences of the user.
     *
     * @param  User  $user
     * @param  int  $distance
     * @param  string  $unit
     * @return string
     */
    public static function format(User $user, int $distance, string $unit): string
    {
        if ($user->distance_format === User::DISTANCE_UNIT_KM && $unit === User::DISTANCE_UNIT_MILES) {
            $distance = round($distance * 1.609344, 2);
        } else if ($user->distance_format === User::DISTANCE_UNIT_MILES && $unit === User::DISTANCE_UNIT_KM) {
            $distance = round($distance / 1.609344, 2);
        }

        return trans('app.distance_format_'.$user->distance_format, ['count' => $distance]);
    }
}
