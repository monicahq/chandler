<?php

namespace Tests\Unit\Models;

use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\MealCategory;
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

    /** @test */
    public function it_belongs_to_a_meal_category()
    {
        $mealCategory = MealCategory::factory()->create();
        $meal = Meal::factory()->create([
            'meal_category_id' => $mealCategory->id,
        ]);
        $this->assertTrue($meal->mealCategory()->exists());
    }
}
