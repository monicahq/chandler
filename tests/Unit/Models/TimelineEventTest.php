<?php

namespace Tests\Unit\Models;

use App\Models\Contact;
use App\Models\TimelineEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TimelineEventTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $timeline = TimelineEvent::factory()->create();

        $this->assertTrue($timeline->vault()->exists());
    }

    /** @test */
    public function it_has_many_participants(): void
    {
        $contact = Contact::factory()->create();
        $timeline = TimelineEvent::factory()->create();

        $timeline->participants()->sync([$contact->id]);

        $this->assertTrue($timeline->participants()->exists());
    }
}
