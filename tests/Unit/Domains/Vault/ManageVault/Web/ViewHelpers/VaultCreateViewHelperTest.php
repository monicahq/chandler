<?php

namespace Tests\Unit\Domains\Vault\ManageVault\Web\ViewHelpers;

use App\Vault\ManageVault\Web\ViewHelpers\VaultCreateViewHelper;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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
