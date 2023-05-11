<?php

namespace Tests\Unit\Domains\Vault\ManageKitchen\Web\ViewHelpers;

use App\Domains\Vault\ManageKitchen\Web\ViewHelpers\KitchenMealsViewHelper;
use App\Models\Meal;
use App\Models\MealCategory;
use App\Models\User;
use App\Models\Vault;
use Carbon\Carbon;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KitchenMealsViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        Carbon::setTestNow(Carbon::create(2022, 1, 1));
        $user = User::factory()->create();
        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
        ]);
        Meal::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'name',
        ]);

        $array = KitchenMealsViewHelper::data($vault);

        $this->assertArrayHasKey('meals', $array);
        $this->assertEquals(
            [
                0 => [

                ],
            ],
            $array['meal_categories']->toArray()
        );
        $this->assertEquals(
            [
                'ingredients' => env('APP_URL').'/vaults/'.$vault->id.'/kitchen/ingredients',
                'store' => env('APP_URL').'/vaults/'.$vault->id.'/kitchen/meals',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_dto(): void
    {
        Carbon::setTestNow(Carbon::create(2022, 1, 1));
        $user = User::factory()->create();
        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
        ]);
        $mealCategory = MealCategory::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $meal = Meal::factory()->create([
            'vault_id' => $vault->id,
            'meal_category_id' => $mealCategory->id,
            'name' => 'name',
            'time_to_prepare_in_minutes' => 30,
            'time_to_cook_in_minutes' => 30,
        ]);

        $array = KitchenMealsViewHelper::dto($meal);

        $this->assertEquals(
            [
                'id' => $meal->id,
                'name' => 'name',
                'time_to_prepare_in_minutes' => $meal->time_to_prepare_in_minutes,
                'time_to_cook_in_minutes' => $meal->time_to_cook_in_minutes,
                'meal_category' => [
                    'id' => $mealCategory->id,
                    'label' => $mealCategory->label,
                ],
                'url' => [
                    'update' => env('APP_URL').'/vaults/'.$vault->id.'/kitchen/meals/'.$meal->id,
                    'destroy' => env('APP_URL').'/vaults/'.$vault->id.'/kitchen/meals/'.$meal->id,
                ],
            ],
            $array
        );
    }
}
