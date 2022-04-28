<?php

namespace App\Vault\ManageCompanies\Web\ViewHelpers;

use App\Models\User;
use App\Models\Vault;
use App\Helpers\VaultHelper;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompanyViewHelper
{
    public static function data(Vault $vault): array
    {
        $collection = $vault->companies()->orderBy('name', 'asc')
            ->get()->map(function ($company) use ($vault) {
                return self::dto($vault, $company);
            });

        return [
            'companies' => $collection,
            'url' => [
                'index' => route('vault.companies.list.index', [
                    'vault' => $vault->id,
                ]),
                'store' => route('vault.companies.store', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }

    public static function dto(Vault $vault, Company $company): array
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'type' => $company->type,
            'url' => route('vault.companies.list.index', [
                'vault' => $vault->id,
            ]),
        ];
    }
}
