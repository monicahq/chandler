<?php

namespace App\Contact\ManageJobInformation\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\Vault;
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
                'index' => route('vault.companies.list.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'store' => route('vault.companies.store', [
                    'vault' => $contact->vault_id,
                ]),
            ],
        ];
    }

    public static function list(Vault $vault, Contact $contact): Collection
    {
        $collection = $vault->companies()->orderBy('name', 'asc')
            ->get()->map(function ($company) use ($vault, $contact) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'type' => $company->type,
                    'selected' => $company->id === $contact->company_id,
                ];
        });

        return $collection;
    }
}
