<?php

namespace Tests\Unit\Helpers;

use App\Helpers\MonetaryNumberHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MonetaryNumberHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_number_according_to_the_user_preference(): void
    {
        $number = 1234567;
        $user = User::factory()->create([
            'number_format' => User::NUMBER_FORMAT_TYPE_COMMA_THOUSANDS_DOT_DECIMAL,
        ]);
        $this->assertEquals(
            '12,345.67',
            MonetaryNumberHelper::formatValue($user, $number)
        );

        $user = User::factory()->create([
            'number_format' => User::NUMBER_FORMAT_TYPE_SPACE_THOUSANDS_COMMA_DECIMAL,
        ]);
        $this->assertEquals(
            '12 345,67',
            MonetaryNumberHelper::formatValue($user, $number)
        );

        $user = User::factory()->create([
            'number_format' => User::NUMBER_FORMAT_TYPE_DOT_THOUSANDS_COMMA_DECIMAL,
        ]);
        $this->assertEquals(
            '12.345,67',
            MonetaryNumberHelper::formatValue($user, $number)
        );
    }
}
