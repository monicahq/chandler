<?php

namespace Tests\Unit\Domains\Settings\ManageUsers\Web\ViewHelpers;

use App\Settings\ManageUsers\Web\ViewHelpers\UserCreateViewHelper;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserCreateViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $array = UserCreateViewHelper::data();
        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'back' => env('APP_URL').'/settings/users',
                'store' => env('APP_URL').'/settings/users',
            ],
            $array['url']
        );
    }
}
