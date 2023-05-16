<?php

namespace Tests\Unit\Domains\Vault\ManageKitchen\Services;

use App\Domains\Vault\ManageKitchen\Services\UpdateMealCategory;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\MealCategory;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateMealCategoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_meal_category(): void
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
        (new UpdateMealCategory())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = Account::factory()->create();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $mealCategory = MealCategory::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $this->executeService($ross, $account, $vault, $mealCategory);
    }

    /** @test */
    public function it_fails_if_meal_category_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $mealCategory = MealCategory::factory()->create();
        $this->executeService($ross, $ross->account, $vault, $mealCategory);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_account(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_VIEW, $vault);
        $mealCategory = MealCategory::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $this->executeService($ross, $ross->account, $vault, $mealCategory);
    }

    private function executeService(User $author, Account $account, Vault $vault, MealCategory $mealCategory): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'vault_id' => $vault->id,
            'meal_category_id' => $mealCategory->id,
            'label' => 'label name',
        ];

        $mealCategory = (new UpdateMealCategory())->execute($request);

        $this->assertDatabaseHas('meal_categories', [
            'id' => $mealCategory->id,
            'vault_id' => $vault->id,
            'label' => 'label name',
        ]);

        $this->assertInstanceOf(
            MealCategory::class,
            $mealCategory
        );
    }
}
