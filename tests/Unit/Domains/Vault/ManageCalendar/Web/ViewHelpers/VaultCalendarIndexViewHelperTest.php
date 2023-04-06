<?php

namespace Tests\Unit\Domains\Vault\ManageCalendar\Web\ViewHelpers;

use App\Domains\Vault\ManageCalendar\Web\ViewHelpers\VaultCalendarIndexViewHelper;
use App\Models\Contact;
use App\Models\ContactImportantDate;
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
            7,
            $array
        );
        $this->assertEquals(
            'April 2023',
            $array['current_month']
        );
        $this->assertEquals(
            4,
            $array['month']
        );
        $this->assertEquals(
            2023,
            $array['year']
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
        $vault = Vault::factory()->create();
        $collection = VaultCalendarIndexViewHelper::buildMonth($vault, 4, 2023);

        $this->assertEquals(5, $collection->count());
        $this->assertEquals(
            1,
            $collection->toArray()[0]['id']
        );
        $this->assertEquals(
            27,
            $collection->toArray()[0]['days']->toArray()[0]['id']
        );
        $this->assertFalse(
            $collection->toArray()[0]['days']->toArray()[0]['current_day']
        );
        $this->assertFalse(
            $collection->toArray()[0]['days']->toArray()[0]['is_in_month']
        );
        $this->assertEquals(
            2,
            $collection->toArray()[0]['days']->toArray()[6]['id']
        );
        $this->assertFalse(
            $collection->toArray()[0]['days']->toArray()[6]['current_day']
        );
        $this->assertTrue(
            $collection->toArray()[0]['days']->toArray()[6]['is_in_month']
        );
    }

    /** @test */
    public function it_gets_the_important_dates(): void
    {
        $contact = Contact::factory()->create();
        ContactImportantDate::factory()->create([
            'contact_id' => $contact->id,
            'day' => '03',
            'month' => '03',
        ]);

        $contactIds = Contact::all()->pluck('id');

        $collection = VaultCalendarIndexViewHelper::getImportantDates(3, 3, $contactIds);

        $this->assertEquals(1, $collection->count());
    }
}
