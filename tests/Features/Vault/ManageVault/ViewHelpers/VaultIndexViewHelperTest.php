<?php

namespace Tests\Features\Vault\ManageVault\ViewHelpers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Features\Vault\ManageVault\ViewHelpers\VaultIndexViewHelper;

class VaultIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_general_layout_information(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $array = VaultIndexViewHelper::loggedUserInformation();
        $this->assertEquals(
            [
                'name' => $user->name,
                'url' => [
                    'logout' => env('APP_URL') . '/logout',
                ],
            ],
            $array
        );
    }

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $array = VaultIndexViewHelper::data();
        $this->assertEquals(
            [
                'url' => [
                    'vault' => [
                        'new' => env('APP_URL').'/vaults/new',
                    ],
                ],
            ],
            $array
        );
    }
}
