<?php

namespace Tests\Unit\Domains\Contact\ManageContact\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactShowMoveViewHelper extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $user = User::factory()->create();
        $vault = Vault::factory()->create();
        $user->vaults()->attach($vault->id, [
            'permission' => Vault::PERMISSION_EDIT,
            'contact_id' => Contact::factory()->create()->id,
        ]);
        $contact = Contact::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->be($user);

        $array = ContactShowMoveViewHelper::data($contact, $user);
        $this->assertCount(3, $array);
        $this->assertEquals(
            [
                'name' => $contact->name,
            ],
            $array['contact']
        );
        $this->assertEquals(
            [
                'name' => $contact->name,
            ],
            $array['contact']
        );
    }
}
