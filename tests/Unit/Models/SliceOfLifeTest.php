<?php

namespace Tests\Unit\Models;

use App\Models\SliceOfLife;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SliceOfLifeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_journal()
    {
        $sliceOfLife = SliceOfLife::factory()->create();

        $this->assertTrue($sliceOfLife->journal()->exists());
    }
}
