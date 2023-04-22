<?php

namespace Tests\Unit\Domains\Vault\ManageKitchen\Services;

use App\Domains\Vault\ManageKitchen\Services\UpdateMealCategoryPosition;
use App\Models\Account;
use App\Models\MealCategory;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateMealCategoryPositionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_meal_category_position(): void
    {
        $ross = $this->createAdministrator();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $mealCategory = MealCategory::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $this->executeService($ross, $ross->account, $vault, $mealCategory);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateMealCategoryPosition())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $mealCategory = MealCategory::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $this->executeService($ross, $account, $vault, $mealCategory);
    }

    /** @test */
    public function it_fails_if_parameter_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $mealCategory = MealCategory::factory()->create();
        $this->executeService($ross, $ross->account, $vault, $mealCategory);
    }

    private function executeService(User $author, Account $account, Vault $vault, MealCategory $mealCategory): void
    {
        $mealCategory1 = MealCategory::factory()->create([
            'vault_id' => $vault->id,
            'position' => 1,
        ]);
        $mealCategory3 = MealCategory::factory()->create([
            'vault_id' => $vault->id,
            'position' => 3,
        ]);
        $mealCategory4 = MealCategory::factory()->create([
            'vault_id' => $vault->id,
            'position' => 4,
        ]);

        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'meal_category_id' => $mealCategory->id,
            'new_position' => 3,
        ];

        $mealCategory = (new UpdateMealCategoryPosition())->execute($request);

        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory1->id,
            'vault_id' => $vault->id,
            'position' => 1,
        ]);
        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory3->id,
            'vault_id' => $vault->id,
            'position' => 2,
        ]);
        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory4->id,
            'vault_id' => $vault->id,
            'position' => 4,
        ]);
        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory->id,
            'vault_id' => $vault->id,
            'position' => 3,
        ]);

        $request['new_position'] = 2;

        $mealCategory = (new UpdateMealCategoryPosition())->execute($request);

        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory1->id,
            'vault_id' => $vault->id,
            'position' => 1,
        ]);
        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory3->id,
            'vault_id' => $vault->id,
            'position' => 3,
        ]);
        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory4->id,
            'vault_id' => $vault->id,
            'position' => 4,
        ]);
        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory->id,
            'vault_id' => $vault->id,
            'position' => 2,
        ]);

        $this->assertInstanceOf(
            MealCategory::class,
            $mealCategory
        );
    }
}
