<?php

namespace App\Helpers;

use App\Models\Contact;
use App\Models\RelationshipType;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NameHelper
{
    /**
     * Format the name of the contact according to the user preferences.
     *
     * @param  User  $user
     * @param  Contact  $contact
     * @return string
     */
    public static function formatName(User $user, Contact $contact): string
    {
        $allCharacters = str_split($user->name_order);

        $variableFound = false;
        $variableName = '';
        $completeName = '';
        foreach ($allCharacters as $char) {
            if ($char === '%' && !$variableFound) {
                // a variable has been found
                $variableFound = true;
                $variableName = $variableName . $char;
            } elseif ($char !== '%' && $variableFound) {
                $variableName = $variableName . $char;
            } elseif ($char === '%' && $variableFound) {
                // the variable has ended
                // get rid of the first %
                $variableName = substr($variableName, 1);
                $completeName = $completeName . $contact->{$variableName};

                // reset the variable
                $variableFound = false;
                $variableName = '';
            } else {
                // this is a normal character
                $completeName = $completeName . $char;
            }
        }

        return $completeName;
    }
}
