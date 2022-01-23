<?php

namespace App\Http\Controllers\Vault\Contact\Modules\ContactName\ViewHelpers;

use App\Models\Note;
use App\Models\Contact;
use App\Helpers\DateHelper;
use App\Models\User;
use Illuminate\Support\Str;

class ModuleContactNameViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        return [
            'name' => $contact->getName($user),
        ];
    }
}
