<?php

namespace App\Contact\ManagePhotos\Web\ViewHelpers;

use App\Helpers\FileHelper;
use App\Models\Contact;
use App\Models\File;

class ContactPhotosShowViewHelper
{
    public static function data(File $file, Contact $contact): array
    {
        return [
            'id' => $file->id,
            'name' => $file->name,
            'mime_type' => $file->mime_type,
            'size' => FileHelper::formatFileSize($file->size),
            'url' => [
                'display' => 'https://ucarecdn.com/' . $file->uuid . '/-/resize/1700x/smart/-/format/auto/-/quality/smart_retina/',
                'download' => $file->cdn_url,
                'destroy' => route('contact.photo.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'photo' => $file->id,
                ]),
            ],
        ];
    }
}
