<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Loan;
use App\Models\Note;
use App\Models\Label;
use App\Models\Gender;
use App\Models\Address;
use App\Models\Avatar;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Pronoun;
use App\Models\Template;
use App\Models\ContactLog;
use App\Models\ContactReminder;
use App\Models\RelationshipType;
use App\Models\ContactInformation;
use App\Models\ContactImportantDate;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AvatarTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $avatar = Avatar::factory()->create();

        $this->assertTrue($avatar->contact()->exists());
    }
}
