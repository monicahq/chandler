<?php

namespace Tests\Unit\Helpers;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Contact;
use App\Helpers\AgeHelper;
use App\Models\ContactDate;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AgeHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_age_based_on_a_complete_date(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $contact = Contact::factory()->create();
        ContactDate::factory()->create([
            'contact_id' => $contact->id,
            'date' => '1981-10-29',
            'type' => ContactDate::TYPE_BIRTHDATE,
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
        $contact = Contact::factory()->create();
        ContactDate::factory()->create([
            'contact_id' => $contact->id,
            'date' => '1970',
            'type' => ContactDate::TYPE_BIRTHDATE,
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
        $contact = Contact::factory()->create();
        ContactDate::factory()->create([
            'contact_id' => $contact->id,
            'date' => '10-02',
            'type' => ContactDate::TYPE_BIRTHDATE,
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
