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
            ],
            $array
        );
    }
}
