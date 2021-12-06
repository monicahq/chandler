<?php

namespace Tests\Features\Vault\ManageVault\ViewHelpers;

use App\Features\Vault\ManageVault\ViewHelpers\VaultCreateViewHelper;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Features\Vault\ManageVault\ViewHelpers\VaultIndexViewHelper;

class VaultCreateViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $array = VaultCreateViewHelper::data();
        $this->assertEquals(
            [
                'url' => [
                    'store' => env('APP_URL').'/vaults',
                    'back' => env('APP_URL').'/vaults',
                ],
            ],
            $array
        );
    }
}
