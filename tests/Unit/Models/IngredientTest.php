<?php

namespace Tests\Unit\Models;

use App\Models\Ingredient;
use App\Models\Meal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IngredientTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_a_vault()
    {
        $ingredient = Ingredient::factory()->create();
        $this->assertTrue($ingredient->vault()->exists());
    }

    /** @test */
    public function it_has_many_meals(): void
    {
        $ingredient = Ingredient::factory()->create([]);
        $meal = Meal::factory()->create();

        $ingredient->meals()->sync([$meal->id]);

        $this->assertTrue($ingredient->meals()->exists());
    }

    /** @test */
    public function it_gets_the_ingredient_name()
    {
        $ingredient = Ingredient::factory()->create([
            'label' => null,
            'label_translation_key' => 'Sikhism',
        ]);

        $this->assertEquals(
            'Sikhism',
            $ingredient->label
        );

        $ingredient = Ingredient::factory()->create([
            'label' => 'God',
            'label_translation_key' => null,
        ]);

        $this->assertEquals(
            'God',
            $ingredient->label
        );
    }
}
