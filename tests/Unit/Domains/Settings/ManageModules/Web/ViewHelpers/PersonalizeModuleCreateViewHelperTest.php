<?php

namespace Tests\Unit\Domains\Settings\ManageModules\Web\ViewHelpers;

use App\Domains\Settings\ManageModules\Web\ViewHelpers\PersonalizeModuleCreateViewHelper;
use App\Models\Module;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PersonalizeModuleCreateViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $module = Module::factory()->create([
            'can_be_deleted' => true,
        ]);
        $array = PersonalizeModuleCreateViewHelper::data($module->account);
        $this->assertEquals(
            1,
            count($array)
        );
        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'personalize' => env('APP_URL').'/settings/personalize',
                'modules' => env('APP_URL').'/settings/personalize/modules',
                'back' => env('APP_URL').'/settings/personalize/modules',
                'store' => env('APP_URL').'/settings/personalize/modules',
            ],
            $array['url']
        );
    }
}
