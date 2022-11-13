<?php

namespace Tests\Unit\Models;

use App\Models\ModuleRowFieldChoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModuleRowFieldChoiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_a_module_row_field()
    {
        $choice = ModuleRowFieldChoice::factory()->create();

        $this->assertTrue($choice->rowField()->exists());
    }
}
