<?php

namespace Tests\Unit\Domains\Contact\ManageGoals\Web\ViewHelpers;

use App\Contact\ManageGoals\Web\ViewHelpers\ModuleGoalsViewHelper;
use App\Models\Contact;
use App\Models\Goal;
use App\Models\User;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModuleGoalsViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();

        $goal = Goal::factory()->create([
            'contact_id' => $contact->id,
            'active' => true,
        ]);
        Goal::factory()->count(2)->create([
            'contact_id' => $contact->id,
            'active' => false,
        ]);

        $array = ModuleGoalsViewHelper::data($contact, $user);

        $this->assertEquals(
            3,
            count($array)
        );

        $this->assertArrayHasKey('active_goals', $array);
        $this->assertArrayHasKey('inactive_goals_count', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            2,
            $array['inactive_goals_count']
        );

        $this->assertEquals(
            [
                'store' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/goals',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_transfer_object(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $goal = Goal::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $collection = ModuleGoalsViewHelper::dto($contact, $goal);

        $this->assertEquals(
            [
                'id' => $goal->id,
                'name' => $goal->name,
                'active' => $goal->active,
                'url' => [
                    'update' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/goals/'.$goal->id,
                    'destroy' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/goals/'.$goal->id,
                ],
            ],
            $collection
        );
    }
}
