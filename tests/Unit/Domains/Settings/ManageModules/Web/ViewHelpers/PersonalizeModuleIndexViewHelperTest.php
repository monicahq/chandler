<?php

namespace Tests\Unit\Domains\Settings\ManageModules\Web\ViewHelpers;

use App\Domains\Settings\ManageModules\Web\ViewHelpers\PersonalizeModuleIndexViewHelper;
use App\Models\Module;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PersonalizeModuleIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $module = Module::factory()->create([
            'can_be_deleted' => true,
        ]);
        $array = PersonalizeModuleIndexViewHelper::data($module->account);
        $this->assertEquals(
            2,
            count($array)
        );
        $this->assertArrayHasKey('modules', $array);
        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'personalize' => env('APP_URL').'/settings/personalize',
                'create' => env('APP_URL').'/settings/personalize/modules/create',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_needed_for_the_data_transfer_object(): void
    {
        $module = Module::factory()->create();
        $array = PersonalizeModuleIndexViewHelper::dtoModule($module);
        $this->assertEquals(
            [
                'id' => $module->id,
                'name' => $module->name,
                'type' => $module->type,
                'reserved_to_contact_information' => $module->reserved_to_contact_information,
                'can_be_deleted' => $module->can_be_deleted,
                'url' => [
                    'show' => env('APP_URL').'/settings/personalize/modules/'.$module->id,
                    'update' => env('APP_URL').'/settings/personalize/modules/'.$module->id,
                    'destroy' => env('APP_URL').'/settings/personalize/modules/'.$module->id,
                ],
            ],
            $array
        );
    }
}
