<?php

namespace Tests\Unit\Controllers\Vault\Settings\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\Note;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Search\ViewHelpers\VaultSearchIndexViewHelper;

class VaultSearchIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_information_necessary_to_load_the_view(): void
    {
        $vault = Vault::factory()->create();
        $contact = Contact::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'nickname' => 'Johnny',
            'maiden_name' => 'Doe',
            'middle_name' => 'Doe',
        ]);
        $note = Note::factory()->create([
            'contact_id' => $contact->id,
            'title' => 'title',
            'body' => 'john',
        ]);

        $array = VaultSearchIndexViewHelper::data($vault, 'john');
        $this->assertEquals(
            3,
            count($array)
        );
        $this->assertArrayHasKey('contacts', $array);
        $this->assertArrayHasKey('notes', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                'search' => env('APP_URL').'/vaults/'.$vault->id.'/search',
            ],
            $array['url']
        );

        $this->assertEquals(
            [
                0 => [
                    'id' => $contact->id,
                    'name' => 'John Doe Johnny Doe Doe',
                    'url' => env('APP_URL').'/vaults/'.$vault->id.'/contacts/'.$contact->id,
                ],
            ],
            $array['contacts']->toArray()
        );
    }
}
