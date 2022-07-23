<?php

namespace Tests\Unit\Domains\Contact\ManageContactInformation\Web\ViewHelpers;

use App\Contact\ManageContactInformation\Web\ViewHelpers\ContactInformationViewHelper;
use App\Models\Contact;
use App\Models\ContactInformation;
use App\Models\ContactInformationType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactInformationViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create();
        ContactInformation::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $array = ContactInformationViewHelper::data($contact);

        $this->assertEquals(
            2,
            count($array)
        );

        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                'store' => env('APP_URL') . '/vaults/' . $contact->vault->id . '/contacts/' . $contact->id . '/contactInformation',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_transfer_object(): void
    {
        $contact = Contact::factory()->create();
        $type = ContactInformationType::factory()->create([
            'name' => 'Facebook shit',
            'protocol' => 'mailto:',
        ]);
        $info = ContactInformation::factory()->create([
            'contact_id' => $contact->id,
            'type_id' => $type->id,
        ]);

        $array = ContactInformationViewHelper::dto($contact, $info);

        $this->assertEquals(
            [
                'id' => $info->id,
                'label' => 'Facebook shit',
                'data' => 'mailto:'.$info->data,
                'url' => [
                    'update' => env('APP_URL') . '/vaults/' . $contact->vault->id . '/contacts/' . $contact->id . '/contactInformation/' . $info->id,
                    'destroy' => env('APP_URL') . '/vaults/' . $contact->vault->id . '/contacts/' . $contact->id . '/contactInformation/' . $info->id,
                ],
            ],
            $array
        );
    }
}
