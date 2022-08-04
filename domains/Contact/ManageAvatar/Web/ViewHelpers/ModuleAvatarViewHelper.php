<?php

namespace App\Contact\ManageAvatar\Web\ViewHelpers;

use App\Models\Contact;

class ModuleAvatarViewHelper
{
    public static function data(Contact $contact): array
    {
        return [
            'content' => $contact->avatar,
            'type' => $contact->currentAvatar->type,
        ];
    }
}
