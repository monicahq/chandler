<?php

namespace Tests\Unit\Models;

use App\Models\Meal;
use App\Models\MealCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MealCategoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_a_vault()
    {
        $meal = Meal::factory()->create();
        $this->assertTrue($meal->vault()->exists());
    }

    /** @test */
    public function it_has_many_meals(): void
    {
        $mealCategory = MealCategory::factory()->create();
        Meal::factory()->count(2)->create([
            'meal_category_id' => $mealCategory->id,
        ]);

        $this->assertTrue($mealCategory->meals()->exists());
    }

    /** @test */
    public function it_gets_the_custom_label_if_defined()
    {
        $mealCategory = MealCategory::factory()->create([
            'label' => 'this is the real name',
            'label_translation_key' => 'life_event_category.label',
        ]);

        $this->assertEquals(
            'this is the real name',
            $mealCategory->label
        );
    }
}
