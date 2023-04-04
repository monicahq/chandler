<?php

namespace Tests\Unit\Domains\Vault\ManageCalendar\Web\ViewHelpers;

use App\Domains\Vault\ManageCalendar\Web\ViewHelpers\VaultCalendarIndexViewHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VaultCalendarIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
    }

    /** @test */
    public function it_builds_the_monthly_calendar(): void
    {
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
                ],
                1 => [
                    'id' => 28,
                    'date' => '28',
                ],
                2 => [
                    'id' => 29,
                    'date' => '29',
                ],
                3 => [
                    'id' => 30,
                    'date' => '30',
                ],
                4 => [
                    'id' => 31,
                    'date' => '31',
                ],
                5 => [
                    'id' => 1,
                    'date' => '01',
                ],
                6 => [
                    'id' => 2,
                    'date' => '02',
                ],
            ],
            $collection->toArray()[0]['days']->toArray()
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => 24,
                    'date' => '24',
                ],
                1 => [
                    'id' => 25,
                    'date' => '25',
                ],
                2 => [
                    'id' => 26,
                    'date' => '26',
                ],
                3 => [
                    'id' => 27,
                    'date' => '27',
                ],
                4 => [
                    'id' => 28,
                    'date' => '28',
                ],
                5 => [
                    'id' => 29,
                    'date' => '29',
                ],
                6 => [
                    'id' => 30,
                    'date' => '30',
                ],
            ],
            $collection->toArray()[4]['days']->toArray()
        );
    }
}
