<?php

namespace Tests\Unit\Controllers\Vault\Contact\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vault;
use App\Models\Contact;
use App\Models\Template;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactShowBlankViewHelper;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactShowViewHelper;
use App\Models\TemplatePage;

class ContactShowViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create();
        $template = Template::factory()->create();
        $templatePageContactInformation = TemplatePage::factory()->create([
            'template_id' => $template->id,
            'type' => TemplatePage::TYPE_CONTACT,
            'position' => 1,
        ]);
        $templatePage = TemplatePage::factory()->create([
            'template_id' => $template->id,
            'position' => 2,
        ]);
        $contact->template_id = $template->id;
        $contact->save();

        $array = ContactShowViewHelper::data($contact);

        $this->assertEquals(
            3,
            count($array)
        );

        $this->assertArrayHasKey('template_pages', $array);
        $this->assertArrayHasKey('contact_information', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                0 => [
                    'id' => $templatePage->id,
                    'name' => $templatePage->name,
                    'selected' => true,
                    'url' => [
                        'show' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/tabs/'.$templatePage->slug,
                    ],
                ],
            ],
            $array['template_pages']->toArray()
        );

        $this->assertEquals(
            [
            ],
            $array['contact_information']
        );
    }
}
