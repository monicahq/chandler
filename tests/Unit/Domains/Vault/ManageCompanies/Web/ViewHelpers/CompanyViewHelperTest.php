<?php

namespace Tests\Unit\Domains\Vault\ManageCompanies\Web\ViewHelpers;

use function env;

use App\Models\Company;
use Tests\TestCase;
use App\Models\User;
use App\Models\Label;
use App\Models\Vault;
use App\Models\Template;
use App\Vault\ManageCompanies\Web\ViewHelpers\CompanyViewHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Vault\ManageVaultSettings\Web\ViewHelpers\VaultSettingsIndexViewHelper;

class CompanyViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_information_necessary_to_load_the_view(): void
    {
        $vault = Vault::factory()->create();
        Company::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $array = CompanyViewHelper::data($vault);
        $this->assertCount(
            2,
            $array
        );
        $this->assertArrayHasKey('companies', $array);
        $this->assertEquals(
            [
                'index' => env('APP_URL') . '/vaults/' . $vault->id . '/companies/list',
                'store' => env('APP_URL') . '/vaults/' . $vault->id . '/companies',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_needed_for_the_data_transfer_object(): void
    {
        $vault = Vault::factory()->create();
        $company = Company::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $array = CompanyViewHelper::dto($vault, $company);
        $this->assertEquals(
            [
                'id' => $company->id,
                'name' => $company->name,
                'type' => $company->type,
                'url' => env('APP_URL') . '/vaults/' . $vault->id . '/companies/list',
            ],
            $array
        );
    }
}
