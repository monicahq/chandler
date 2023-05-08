<?php

namespace App\ExportResources\User;

use App\ExportResources\ExportResource;
use App\Models\Contact\Contact;

/**
 * @mixin \App\Models\User
 */
class User extends ExportResource
{
    protected $columns = [
        'uuid' => 'id',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    protected $properties = [
        'locale',
        'name_order',
        'timezone',
        'date_format',
        'number_format',
        'distance_format',
        'default_map_site',
        'help_shown',
    ];

    public function data(): ?array
    {
        return  [
            'properties' => [
                // $this->mergeWhen($this->me !== null, function () {
                //     return ['me_contact' => $this->me->uuid];
                // }),
            ],
        ];
    }
}
