<?php

namespace App\Contact\ManageJobInformation\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\Vault;
use App\Models\Company;

class ModuleCompanyViewHelper
{
    public static function data(Contact $contact): array
    {
       $company = $contact->company;

        return [
            'job_position' => $contact->job_position,
            'company' => $company ? [
                'id' => $company->id,
                'name' => $company->name,
                'type' => $company->type,
            ] : null,
        ];
    }
}
