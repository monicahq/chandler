<?php

namespace Tests\Unit\Helpers;

use App\Helpers\AgeHelper;
use Carbon\Carbon;
use Tests\TestCase;
use App\Helpers\DateHelper;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AgeHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_age_based_on_a_complete_date(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $contact = Contact::factory()->create([
            'born_at' => '1981-10-29',
        ]);

        $this->assertEquals(
            36,
            AgeHelper::getAge($contact)
        );
    }

    /** @test */
    public function it_gets_the_age_based_on_a_year(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $contact = Contact::factory()->create([
            'born_at' => '1970',
        ]);

        $this->assertEquals(
            48,
            AgeHelper::getAge($contact)
        );
    }

    /** @test */
    public function it_cant_get_the_age_based_on_a_month_or_day(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $contact = Contact::factory()->create([
            'born_at' => '10-02',
        ]);

        $this->assertNull(
            AgeHelper::getAge($contact)
        );
    }

    /** @test */
    public function it_cant_get_the_age_if_the_date_is_not_set(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $contact = Contact::factory()->create();

        $this->assertNull(
            AgeHelper::getAge($contact)
        );
    }
}
