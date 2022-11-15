<?php

namespace Tests\Unit\Domains\Settings\ManageModules\Web\ViewHelpers;

use App\Domains\Settings\ManageModules\Web\ViewHelpers\PersonalizeModuleShowViewHelper;
use App\Models\Module;
use App\Models\ModuleRow;
use App\Models\ModuleRowField;
use App\Models\ModuleRowFieldChoice;
use App\Models\User;
use Carbon\Carbon;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PersonalizeModuleShowViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        Carbon::setTestNow(Carbon::create(2022, 1, 1));
        $user = User::factory()->create();
        $module = Module::factory()->create([
            'name' => 'misere',
        ]);
        $row = ModuleRow::factory()->create([
            'module_id' => $module->id,
        ]);
        $field = ModuleRowField::factory()->create([
            'module_row_id' => $row->id,
            'label' => 'france',
            'help' => 'aide',
        ]);
        $choice = ModuleRowFieldChoice::factory()->create([
            'module_row_field_id' => $field->id,
        ]);

        $array = PersonalizeModuleShowViewHelper::data($module, $user);
        $this->assertEquals(
            5,
            count($array)
        );
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('module_rows', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            $module->id,
            $array['id']
        );
        $this->assertEquals(
            'misere',
            $array['name']
        );
        $this->assertEquals(
            'Jan 01, 2022',
            $array['created_at']
        );
        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'personalize' => env('APP_URL').'/settings/personalize',
                'modules' => env('APP_URL').'/settings/personalize/modules',
                'destroy' => env('APP_URL').'/settings/personalize/modules/'.$module->id,
            ],
            $array['url']
        );
        $this->assertEquals(
            $row->id,
            $array['module_rows']->toArray()[0]['id']
        );

        $moduleRowFields = $array['module_rows']->toArray()[0]['module_row_fields'];
        $this->assertEquals(
            $field->id,
            $moduleRowFields->toArray()[0]['id']
        );
        $this->assertEquals(
            'france',
            $moduleRowFields->toArray()[0]['label']
        );
        $this->assertEquals(
            'aide',
            $moduleRowFields->toArray()[0]['help']
        );
        $this->assertEquals(
            $field->placeholder,
            $moduleRowFields->toArray()[0]['placeholder']
        );
        $this->assertEquals(
            $field->required,
            $moduleRowFields->toArray()[0]['required']
        );
        $this->assertEquals(
            $field->module_field_type,
            $moduleRowFields->toArray()[0]['module_field_type']
        );
        $this->assertEquals(
            $choice->id,
            $moduleRowFields->toArray()[0]['choices']->toArray()[0]['id']
        );
        $this->assertEquals(
            $choice->label,
            $moduleRowFields->toArray()[0]['choices']->toArray()[0]['label']
        );
    }
}
