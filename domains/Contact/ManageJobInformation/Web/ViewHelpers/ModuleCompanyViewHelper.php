<?php

namespace App\Contact\ManageJobInformation\Web\ViewHelpers;

use App\Models\Vault;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Support\Collection;

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
            'url' => [
                'index' => route('contact.companies.list.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'update' => route('contact.job_information.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function list(Vault $vault, Contact $contact): Collection
    {
        $collection = $vault->companies()->orderBy('name', 'asc')
            ->get()->map(function ($company) use ($contact) {
                return self::dto($company, $contact);
            });

        return $collection;
    }

    public static function dto(Company $company, Contact $contact): array
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'type' => $company->type,
            'selected' => $company->id === $contact->company_id,
        ];
    }
}
