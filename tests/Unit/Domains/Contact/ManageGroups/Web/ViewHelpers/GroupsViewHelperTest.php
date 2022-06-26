<?php

namespace Tests\Unit\Domains\Contact\ManageGroups\Web\ViewHelpers;

use App\Contact\ManageGoals\Web\ViewHelpers\ModuleGoalsViewHelper;
use App\Contact\ManageGroups\Web\ViewHelpers\GroupsViewHelper;
use App\Contact\ManageGroups\Web\ViewHelpers\ModuleGroupsViewHelper;
use App\Models\Contact;
use App\Models\Goal;
use App\Models\Group;
use App\Models\User;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GroupsViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_all_the_groups_associated_with_the_contact(): void
    {
        $contact = Contact::factory()->create();
        $group = Group::factory()->create([
            'vault_id' => $contact->vault_id,
        ]);
        $group->contacts()->sync([$contact->id]);

        $collection = GroupsViewHelper::summary($contact);

        $this->assertEquals(
            1,
            $collection->count()
        );

        $this->assertEquals(
            [
                0 => [
                    'id' => $group->id,
                    'name' => $group->name,
                ]
            ],
            $collection->toArray()
        );
    }
}
