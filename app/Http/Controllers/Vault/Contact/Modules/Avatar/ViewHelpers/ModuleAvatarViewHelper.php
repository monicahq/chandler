<?php

namespace App\Http\Controllers\Vault\Contact\Modules\Avatar\ViewHelpers;

use App\Models\Note;
use App\Models\Contact;
use App\Helpers\DateHelper;
use App\Models\User;
use Illuminate\Support\Str;

class ModuleAvatarViewHelper
{
    public static function data(Contact $contact): string
    {
        return '<img class="h-20 w-20 mx-auto rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">';
    }
}
