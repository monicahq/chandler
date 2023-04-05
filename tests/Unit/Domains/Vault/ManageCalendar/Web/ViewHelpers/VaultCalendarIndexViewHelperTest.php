<?php

namespace Tests\Unit\Domains\Vault\ManageCalendar\Web\ViewHelpers;

use App\Domains\Vault\ManageCalendar\Web\ViewHelpers\VaultCalendarIndexViewHelper;
use App\Models\Vault;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VaultCalendarIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $vault = Vault::factory()->create();
        $array = VaultCalendarIndexViewHelper::data($vault, 2023, 4);

        $this->assertCount(
            5,
            $array
        );
        $this->assertEquals(
            'April 2023',
            $array['current_month']
        );
        $this->assertEquals(
            'May 2023',
            $array['next_month']
        );
        $this->assertEquals(
            'March 2023',
            $array['previous_month']
        );
        $this->assertEquals(
            [
                'previous' => env('APP_URL').'/vaults/'.$vault->id.'/calendar/years/2023/months/3',
                'next' => env('APP_URL').'/vaults/'.$vault->id.'/calendar/years/2023/months/5',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_builds_the_monthly_calendar(): void
    {
        Carbon::setTestNow(Carbon::create(2023, 4, 2));
        $collection = VaultCalendarIndexViewHelper::buildMonth(4, 2023);

        $this->assertEquals(5, $collection->count());
        $this->assertEquals(
            1,
            $collection->toArray()[0]['id']
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => 27,
                    'date' => '27',
                    'current_day' => false,
                    'is_in_month' => false,
                ],
                1 => [
                    'id' => 28,
                    'date' => '28',
                    'current_day' => false,
                    'is_in_month' => false,
                ],
                2 => [
                    'id' => 29,
                    'date' => '29',
                    'current_day' => false,
                    'is_in_month' => false,
                ],
                3 => [
                    'id' => 30,
                    'date' => '30',
                    'current_day' => false,
                    'is_in_month' => false,
                ],
                4 => [
                    'id' => 31,
                    'date' => '31',
                    'current_day' => false,
                    'is_in_month' => false,
                ],
                5 => [
                    'id' => 1,
                    'date' => '01',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
                6 => [
                    'id' => 2,
                    'date' => '02',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
            ],
            $collection->toArray()[0]['days']->toArray()
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => 24,
                    'date' => '24',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
                1 => [
                    'id' => 25,
                    'date' => '25',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
                2 => [
                    'id' => 26,
                    'date' => '26',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
                3 => [
                    'id' => 27,
                    'date' => '27',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
                4 => [
                    'id' => 28,
                    'date' => '28',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
                5 => [
                    'id' => 29,
                    'date' => '29',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
                6 => [
                    'id' => 30,
                    'date' => '30',
                    'current_day' => false,
                    'is_in_month' => true,
                ],
            ],
            $collection->toArray()[4]['days']->toArray()
        );
    }
}
