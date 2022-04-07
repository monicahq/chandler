<?php

namespace Tests\Unit\Domains\Vault\ManageVault\Web\ViewHelpers;

use function env;

use App\Vault\ManageVault\Web\ViewHelpers\VaultCreateViewHelper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
