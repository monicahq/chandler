<?php

namespace Tests\Unit\Models;

use App\Models\ModuleRowField;
use App\Models\ModuleRowFieldChoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModuleRowFieldTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_a_module_row()
    {
        $field = ModuleRowField::factory()->create();

        $this->assertTrue($field->row()->exists());
    }

    /** @test */
    public function it_has_many_choices()
    {
        $field = ModuleRowField::factory()->create();
        ModuleRowFieldChoice::factory()->create([
            'module_row_field_id' => $field->id,
        ]);

        $this->assertTrue($field->choices()->exists());
    }
}
