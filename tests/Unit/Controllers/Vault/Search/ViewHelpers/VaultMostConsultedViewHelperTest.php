<?php

namespace Tests\Unit\Controllers\Vault\Contact\ImportantDates\ViewHelpers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Search\ViewHelpers\VaultMostConsultedViewHelper;

class VaultMostConsultedViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_five_most_consulted_contacts(): void
    {
        $contact = Contact::factory()->create([
            'first_name' => 'regis',
            'last_name' => 'freyd',
        ]);
        $mostViewedContact = Contact::factory()->create([
            'first_name' => 'alexis',
            'last_name' => 'saettler',
            'vault_id' => $contact->vault_id,
        ]);
        $user = User::factory()->create();
        DB::table('contact_vault_user')->insert([
            'contact_id' => $contact->id,
            'vault_id' => $contact->vault_id,
            'user_id' => $user->id,
            'number_of_views' => 1,
        ]);
        DB::table('contact_vault_user')->insert([
            'contact_id' => $mostViewedContact->id,
            'vault_id' => $contact->vault_id,
            'user_id' => $user->id,
            'number_of_views' => 4,
        ]);

        $collection = VaultMostConsultedViewHelper::data($contact->vault, $user);

        $this->assertEquals(
            [
                0 => [
                    'id' => $mostViewedContact->id,
                    'name' => 'alexis saettler',
                ],
                1 => [
                    'id' => $contact->id,
                    'name' => 'regis freyd',
                ],
            ],
            $collection->toArray()
        );
    }
}
