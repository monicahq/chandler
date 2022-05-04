<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Couple;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CoupleTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_vault()
    {
        $couple = Couple::factory()->create();

        $this->assertTrue($couple->vault()->exists());
    }
}
