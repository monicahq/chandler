<?php

namespace Tests\Unit\Domains\Settings\ManageSubscription\Web\ViewHelpers;

use App\Domains\Settings\ManageSubscription\Web\ViewHelpers\SubscriptionViewHelper;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubscriptionViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $array = SubscriptionViewHelper::data();
        $this->assertEquals(
            [
                'url' => [
                    'back' => env('APP_URL').'/settings',
                    'store' => env('APP_URL').'/settings/subscription',
                    'customer_portal' => 'https://customers.monicahq.com',
                ],
            ],
            $array
        );
    }
}
