<?php

namespace Tests\Unit\Domains\Vault\ManageKitchen\Services;

use App\Domains\Vault\ManageKitchen\Services\UpdateIngredient;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\Ingredient;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateIngredientTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_ingredient(): void
    {
        $ross = $this->createAdministrator();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $ingredient = Ingredient::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $this->executeService($ross, $ross->account, $vault, $ingredient);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateIngredient())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = Account::factory()->create();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $ingredient = Ingredient::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $this->executeService($ross, $account, $vault, $ingredient);
    }

    /** @test */
    public function it_fails_if_meal_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $ingredient = Ingredient::factory()->create();
        $this->executeService($ross, $ross->account, $vault, $ingredient);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_account(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_VIEW, $vault);
        $ingredient = Ingredient::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $this->executeService($ross, $ross->account, $vault, $ingredient);
    }

    private function executeService(User $author, Account $account, Vault $vault, Ingredient $ingredient): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'vault_id' => $vault->id,
            'ingredient_id' => $ingredient->id,
            'label' => 'label name',
        ];

        $ingredient = (new UpdateIngredient())->execute($request);

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'vault_id' => $vault->id,
            'label' => 'label name',
        ]);

        $this->assertInstanceOf(
            Ingredient::class,
            $ingredient
        );
    }
}
