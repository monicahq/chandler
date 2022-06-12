<?php

namespace Tests\Unit\Domains\Settings\ManageGroupTypes\Web\ViewHelpers;

use App\Models\GiftState;
use App\Models\GroupType;
use App\Settings\ManageGiftStates\Web\ViewHelpers\PersonalizeGiftStateViewHelper;
use App\Settings\ManageGroupTypes\Web\ViewHelpers\PersonalizeGroupTypeViewHelper;

use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PersonalizeGroupTypeViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $groupType = GroupType::factory()->create();
        $array = PersonalizeGroupTypeViewHelper::data($groupType->account);
        $this->assertEquals(
            2,
            count($array)
        );
        $this->assertArrayHasKey('group_types', $array);
        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'personalize' => env('APP_URL').'/settings/personalize',
                'store' => env('APP_URL').'/settings/personalize/groupTypes',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_needed_for_the_data_transfer_object(): void
    {
        $groupType = GroupType::factory()->create();
        $array = PersonalizeGroupTypeViewHelper::dto($groupType);
        $this->assertEquals(
            [
                'id' => $groupType->id,
                'label' => $groupType->label,
                'position' => $groupType->position,
                'url' => [
                    'position' => env('APP_URL').'/settings/personalize/groupTypes/'.$groupType->id.'/position',
                    'update' => env('APP_URL').'/settings/personalize/groupTypes/'.$groupType->id,
                    'destroy' => env('APP_URL').'/settings/personalize/groupTypes/'.$groupType->id,
                ],
            ],
            $array
        );
    }
}
