<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Note;
use App\Models\Label;
use App\Models\Gender;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Pronoun;
use App\Models\Template;
use App\Models\ContactLog;
use App\Models\RelationshipType;
use App\Models\ContactInformation;
use App\Models\Emotion;
use App\Models\Feeling;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmotionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_account()
    {
        $emotion = Emotion::factory()->create();

        $this->assertTrue($emotion->account()->exists());
    }
}
