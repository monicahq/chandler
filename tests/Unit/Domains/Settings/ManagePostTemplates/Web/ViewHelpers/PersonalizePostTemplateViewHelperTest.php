<?php

namespace Tests\Unit\Domains\Settings\ManagePostTemplates\Web\ViewHelpers;

use App\Models\PostTemplate;
use App\Settings\ManagePostTemplates\Web\ViewHelpers\PersonalizePostTemplateViewHelper;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PersonalizePostTemplateViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $postTemplate = PostTemplate::factory()->create();
        $array = PersonalizePostTemplateViewHelper::data($postTemplate->account);
        $this->assertEquals(
            2,
            count($array)
        );
        $this->assertArrayHasKey('post_templates', $array);
        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'personalize' => env('APP_URL').'/settings/personalize',
                'store' => env('APP_URL').'/settings/personalize/postTemplates',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_needed_for_the_data_transfer_object(): void
    {
        $postTemplate = PostTemplate::factory()->create();
        $array = PersonalizePostTemplateViewHelper::dto($postTemplate);
        $this->assertEquals(
            [
                'id' => $postTemplate->id,
                'label' => $postTemplate->label,
                'position' => $postTemplate->position,
                'url' => [
                    'position' => env('APP_URL').'/settings/personalize/postTemplates/'.$postTemplate->id.'/position',
                    'show' => env('APP_URL').'/settings/personalize/postTemplates/'.$postTemplate->id,
                    'update' => env('APP_URL').'/settings/personalize/postTemplates/'.$postTemplate->id,
                    'destroy' => env('APP_URL').'/settings/personalize/postTemplates/'.$postTemplate->id,
                ],
            ],
            $array
        );
    }
}
