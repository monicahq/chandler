<?php

namespace Tests\Unit\Domains\Settings\ManagePersonalization\Web\ViewHelpers;

use App\Settings\ManagePersonalization\Web\ViewHelpers\PersonalizeIndexViewHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use function env;

class PersonalizeIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $array = PersonalizeIndexViewHelper::data();
        $this->assertEquals(
            [
                'url' => [
                    'settings' => env('APP_URL').'/settings',
                    'back' => env('APP_URL').'/settings',
                    'manage_relationships' => env('APP_URL').'/settings/personalize/relationships',
                    'manage_genders' => env('APP_URL').'/settings/personalize/genders',
                    'manage_pronouns' => env('APP_URL').'/settings/personalize/pronouns',
                    'manage_address_types' => env('APP_URL').'/settings/personalize/addressTypes',
                    'manage_pet_categories' => env('APP_URL').'/settings/personalize/petCategories',
                    'manage_contact_information_types' => env('APP_URL').'/settings/personalize/contactInformationType',
                    'manage_templates' => env('APP_URL').'/settings/personalize/templates',
                    'manage_modules' => env('APP_URL').'/settings/personalize/modules',
                    'manage_currencies' => env('APP_URL').'/settings/personalize/currencies',
                    'manage_call_reasons' => env('APP_URL').'/settings/personalize/callReasonTypes',
                    'manage_activity_types' => env('APP_URL').'/settings/personalize/activityTypes',
                    'manage_life_event_categories' => env('APP_URL').'/settings/personalize/lifeEventCategories',
                    'manage_gift_occasions' => env('APP_URL').'/settings/personalize/giftOccasions',
                    'manage_gift_states' => env('APP_URL').'/settings/personalize/giftStates',
                    'manage_group_types' => env('APP_URL').'/settings/personalize/groupTypes',
                ],
            ],
            $array
        );
    }
}
