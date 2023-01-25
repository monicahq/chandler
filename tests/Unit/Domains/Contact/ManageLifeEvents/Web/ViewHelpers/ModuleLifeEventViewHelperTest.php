<?php

namespace Tests\Unit\Domains\Contact\ManageLoans\Web\ViewHelpers;

use App\Domains\Contact\ManageLifeEvents\Web\ViewHelpers\ModuleLifeEventViewHelper;
use App\Domains\Contact\ManageLoans\Web\ViewHelpers\ModuleLoanViewHelper;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModuleLifeEventViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $contact = Contact::factory()->create();
        $user = User::factory()->create();

        $array = ModuleLifeEventViewHelper::data($contact, $user);

        $this->assertEquals(
            5,
            count($array)
        );

        $this->assertArrayHasKey('contact', $array);
        $this->assertArrayHasKey('current_date', $array);
        $this->assertArrayHasKey('current_date_human_format', $array);
        $this->assertArrayHasKey('life_event_categories', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            '2018-01-01',
            $array['current_date']
        );
        $this->assertEquals(
            'Jan 01, 2018',
            $array['current_date_human_format']
        );

        $this->assertEquals(
            [
                'load' => env('APP_URL') . '/vaults/' . $contact->vault->id . '/contacts/' . $contact->id . '/lifeEvents',
                'store' => env('APP_URL') . '/vaults/' . $contact->vault->id . '/contacts/' . $contact->id . '/lifeEvents',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_transfer_object_for_categories(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $lifeEventCategory = LifeEventCategory::factory()->create([
            'vault_id' => $contact->vault_id,
            'label' => 'name',
        ]);

        $array = ModuleLifeEventViewHelper::dtoLifeEventCategory($lifeEventCategory);

        $this->assertEquals(
            3,
            count($array)
        );
        $this->assertEquals(
            $lifeEventCategory->id,
            $array['id']
        );
        $this->assertEquals(
            'name',
            $array['label']
        );
        $this->assertEquals(
            [],
            $array['life_event_types']->toArray()
        );
    }

    /** @test */
    public function it_gets_the_data_transfer_object_for_types(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $lifeEventCategory = LifeEventCategory::factory()->create([
            'vault_id' => $contact->vault_id,
            'label' => 'name',
        ]);
        $lifeEventType = LifeEventType::factory()->create([
            'life_event_category_id' => $lifeEventCategory->id,
            'label' => 'name',
        ]);

        $array = ModuleLifeEventViewHelper::dtoLifeEventType($lifeEventType);

        $this->assertEquals(
            2,
            count($array)
        );
        $this->assertEquals(
            $lifeEventCategory->id,
            $array['id']
        );
        $this->assertEquals(
            'name',
            $array['label']
        );
    }
}
