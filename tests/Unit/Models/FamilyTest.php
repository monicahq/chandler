<?php

namespace Tests\Unit\Models;

use App\Models\Contact;
use Tests\TestCase;
use App\Models\Family;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FamilyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_vault()
    {
        $family = Family::factory()->create();

        $this->assertTrue($family->vault()->exists());
    }

    /** @test */
    public function it_has_many_contacts(): void
    {
        $ross = Contact::factory()->create([]);
        $family = Family::factory()->create();

        $family->contacts()->sync([$ross->id]);

        $this->assertTrue($family->contacts()->exists());
    }
}
