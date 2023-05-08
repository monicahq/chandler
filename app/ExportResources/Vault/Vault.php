<?php

namespace App\ExportResources\Vault;

use App\ExportResources\Contact\Contact;
use App\ExportResources\ExportResource;

/**
 * @mixin \App\Models\Vault
 */
class Vault extends ExportResource
{
    protected $columns = [
        'uuid' => 'id',
        'type',
        'name',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $properties = [
    ];

    public function data(): ?array
    {
        return  [
            'data' => [
                Contact::countCollection($this->contacts),
                $this->mergeWhen($this->template !== null, [
                    'template' => $this->template->id,
                ]),
            ],
        ];
    }
}
