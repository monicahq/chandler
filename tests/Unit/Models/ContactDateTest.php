<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Note;
use App\Models\Label;
use App\Models\Gender;
use App\Models\Address;
use App\Models\Contact;
use App\Models\ContactDate;
use App\Models\Pronoun;
use App\Models\Template;
use App\Models\ContactLog;
use App\Models\RelationshipType;
use App\Models\ContactInformation;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactDateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $date = ContactDate::factory()->create();

        $this->assertTrue($date->contact()->exists());
    }
}
