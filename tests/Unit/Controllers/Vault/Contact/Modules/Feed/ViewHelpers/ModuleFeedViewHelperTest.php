<?php

namespace Tests\Unit\Controllers\Vault\Contact\Modules\Feed\ViewHelpers;

use function env;

use App\Http\Controllers\Vault\Contact\Modules\Feed\ViewHelpers\ModuleFeedViewHelper;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Note;
use App\Models\User;
use App\Models\Contact;
use App\Models\Emotion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\Modules\Note\ViewHelpers\ModuleNotesViewHelper;
use App\Models\ContactFeedItem;

class ModuleFeedViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $note = Note::factory()->create();
        ContactFeedItem::factory()->create([
            'contact_id' => $contact->id,
            'action' => ContactFeedItem::ACTION_NOTE_CREATED,
            'feedable_id' => $note->id,
            'feedable_type' => Note::class,
        ]);

        $array = ModuleFeedViewHelper::data($contact, $user);

        $this->assertEquals(
            1,
            count($array)
        );

        $this->assertArrayHasKey('items', $array);
        $this->assertArrayHasKey('id', $array['items']->toArray()[0]);
        $this->assertArrayHasKey('action', $array['items']->toArray()[0]);
        $this->assertArrayHasKey('object', $array['items']->toArray()[0]);
    }
}
