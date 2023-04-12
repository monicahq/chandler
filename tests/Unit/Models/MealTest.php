<?php

namespace Tests\Unit\Models;

use App\Models\Ingredient;
use App\Models\Meal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MealTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_a_vault()
    {
        $meal = Meal::factory()->create();
        $this->assertTrue($meal->vault()->exists());
    }

    /** @test */
    public function it_has_many_ingredients(): void
    {
        $meal = Meal::factory()->create();
        $ingredient = Ingredient::factory()->create();

        $meal->ingredients()->sync([$ingredient->id]);

        $this->assertTrue($meal->ingredients()->exists());
    }
}
