<?php

namespace Tests\Unit\Domains\Contact\ManageJobInformation\Web\ViewHelpers;

use Tests\TestCase;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Contact\ManageJobInformation\Web\ViewHelpers\ModuleCompanyViewHelper;

class ModuleCompanyViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create([
            'job_position' => 'CEO',
        ]);

        $array = ModuleCompanyViewHelper::data($contact);

        $this->assertEquals(
            2,
            count($array)
        );

        $this->assertArrayHasKey('job_position', $array);
        $this->assertArrayHasKey('company', $array);

        $this->assertEquals(
            [
                'job_position' => 'CEO',
                'company' => [
                    'id' => $contact->company->id,
                    'name' => $contact->company->name,
                    'type' => $contact->company->type,
                ],
            ],
            $array
        );
    }
}
