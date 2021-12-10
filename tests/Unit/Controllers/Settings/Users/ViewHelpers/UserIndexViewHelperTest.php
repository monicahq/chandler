<?php

namespace Tests\Unit\Controllers\Settings\Users\ViewHelpers;

use function env;

use App\Http\Controllers\Settings\Users\ViewHelpers\UserIndexViewHelper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\ViewHelpers\VaultCreateViewHelper;
use App\Models\User;

class UserIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $user = User::factory()->create();

        $array = UserIndexViewHelper::data($user->account);
        $this->assertEquals(
            2,
            count($array)
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'is_account_administrator' => false,
                    'invitation_code' => null,
                    'invitation_accepted_at' => null,
                    'url' => [
                        'show' => env('APP_URL') . '/settings/users/'.$user->id,
                    ],
                ],
            ],
            $array['users']->toArray()
        );
        $this->assertEquals(
            [
                'settings' => [
                    'index' => env('APP_URL') . '/settings',
                ],
                'users' => [
                    'store' => env('APP_URL') . '/settings/users',
                ],
            ],
            $array['url']
        );
    }
}
