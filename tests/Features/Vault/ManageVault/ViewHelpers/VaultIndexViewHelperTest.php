<?php

namespace Tests\Features\Vault\ManageVault\ViewHelpers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Features\Vault\ManageVault\ViewHelpers\VaultIndexViewHelper;
use App\Models\Vault;

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
                    'logout' => env('APP_URL').'/logout',
                ],
            ],
            $array
        );
    }

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $vault = Vault::factory()->create();

        $array = VaultIndexViewHelper::data($vault->account);

        $this->assertEquals(2, count($array));

        $this->assertEquals(
            [
                0 => [
                    'id' => $vault->id,
                    'name' => $vault->name,
                    'description' => $vault->description,
                    'url' => [
                        'show' => env('APP_URL').'/vaults/'.$vault->id,
                    ],
                ],
            ],
            $array['vaults']->toArray()
        );

        $this->assertEquals(
            [
                'vault' => [
                    'new' => env('APP_URL').'/vaults/new',
                ],
            ],
            $array['url']
        );
    }
}
