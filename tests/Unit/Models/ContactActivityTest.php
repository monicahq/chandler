<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactActivity;
use App\Models\Currency;
use App\Models\Emotion;
use App\Models\Loan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactActivityTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_vault()
    {
        $contactActivity = ContactActivity::factory()->create();

        $this->assertTrue($contactActivity->vault()->exists());
    }

    /** @test */
    public function it_has_one_activity()
    {
        $activity = Activity::factory()->create();
        $contactActivity = ContactActivity::factory()->create([
            'activity_id' => $activity->id,
        ]);

        $this->assertTrue($contactActivity->activity()->exists());
    }

    /** @test */
    public function it_has_one_emotion()
    {
        $emotion = Emotion::factory()->create();
        $contactActivity = ContactActivity::factory()->create([
            'emotion_id' => $emotion->id,
        ]);

        $this->assertTrue($contactActivity->emotion()->exists());
    }

    /** @test */
    public function it_has_many_contacts(): void
    {
        $ross = Contact::factory()->create([]);
        $monica = Contact::factory()->create();
        $contactActivity = ContactActivity::factory()->create();

        $contactActivity->participants()->sync([$ross->id]);
        $contactActivity->participants()->sync([$monica->id]);

        $this->assertTrue($contactActivity->participants()->exists());
    }
}
