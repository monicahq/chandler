<?php

namespace Tests\Unit\Domains\Contact\ManageKitchen\Services;

use App\Domains\Vault\ManageKitchen\Services\AddIngredientToMeal;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AddIngredientToMealTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_adds_an_ingredient_to_a_meal(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $meal = Meal::factory()->create(['vault_id' => $vault->id]);
        $ingredient = Ingredient::factory()->create(['vault_id' => $vault->id]);

        $this->executeService($regis, $regis->account, $vault, $meal, $ingredient);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new AddIngredientToMeal())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $meal = Meal::factory()->create(['vault_id' => $vault->id]);
        $ingredient = Ingredient::factory()->create(['vault_id' => $vault->id]);

        $this->executeService($regis, $account, $vault, $meal, $ingredient);
    }

    /** @test */
    public function it_fails_if_meal_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $meal = Meal::factory()->create();
        $ingredient = Ingredient::factory()->create(['vault_id' => $vault->id]);

        $this->executeService($regis, $regis->account, $vault, $meal, $ingredient);
    }

    /** @test */
    public function it_fails_if_ingredient_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $meal = Meal::factory()->create(['vault_id' => $vault->id]);
        $ingredient = Ingredient::factory()->create();

        $this->executeService($regis, $regis->account, $vault, $meal, $ingredient);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $meal = Meal::factory()->create(['vault_id' => $vault->id]);
        $ingredient = Ingredient::factory()->create(['vault_id' => $vault->id]);

        $this->executeService($regis, $regis->account, $vault, $meal, $ingredient);
    }

    private function executeService(User $author, Account $account, Vault $vault, Meal $meal, Ingredient $ingredient): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'vault_id' => $vault->id,
            'meal_id' => $meal->id,
            'ingredient_id' => $ingredient->id,
        ];

        (new AddIngredientToMeal())->execute($request);

        $this->assertDatabaseHas('ingredient_meal', [
            'meal_id' => $meal->id,
            'ingredient_id' => $ingredient->id,
        ]);
    }
}
