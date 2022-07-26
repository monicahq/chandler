<?php

namespace Tests\Unit\Domains\Contact\ManageNotes\Web\ViewHelpers;

use App\Contact\ManageNotes\Web\ViewHelpers\ModuleNotesViewHelper;
use App\Models\Contact;
use App\Models\Emotion;
use App\Models\Note;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use function env;

class ModuleNotesViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();

        Note::factory()->create([
            'contact_id' => $contact->id,
        ]);
        $emotion = Emotion::factory()->create([
            'account_id' => $contact->vault->account_id,
        ]);

        $array = ModuleNotesViewHelper::data($contact, $user);

        $this->assertEquals(
            3,
            count($array)
        );

        $this->assertArrayHasKey('notes', $array);
        $this->assertArrayHasKey('emotions', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                0 => [
                    'id' => $emotion->id,
                    'name' => $emotion->name,
                    'type' => $emotion->type,
                ],
            ],
            $array['emotions']->toArray()
        );

        $this->assertEquals(
            [
                'store' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/notes',
                'index' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/notes',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_transfer_object(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $collection = ModuleNotesViewHelper::dto($contact, $note, $user);

        $this->assertEquals(
            [
                'id' => $note->id,
                'body' => $note->body,
                'body_excerpt' => null,
                'show_full_content' => false,
                'title' => $note->title,
                'emotion' => null,
                'author' => null,
                'written_at' => 'Jan 01, 2018',
                'url' => [
                    'update' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/notes/'.$note->id,
                    'destroy' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/notes/'.$note->id,
                ],
            ],
            $collection
        );
    }
}
