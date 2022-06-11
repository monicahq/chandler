<?php

namespace Tests\Unit\Models;

use App\Models\Family;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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
