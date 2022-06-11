<?php

namespace Tests\Unit\Models;

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
}
