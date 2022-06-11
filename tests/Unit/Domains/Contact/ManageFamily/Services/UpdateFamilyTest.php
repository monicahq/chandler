<?php

namespace Tests\Unit\Domains\Contact\ManageFamily\Services;

use App\Contact\ManageFamily\Services\UpdateFamily;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\Family;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateFamilyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_family(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $family = Family::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $family);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateFamily)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $family = Family::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $account, $vault, $family);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_initial_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $family = Family::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $family);
    }

    /** @test */
    public function it_fails_if_vault_is_not_in_the_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $family = Family::factory()->create();

        $this->executeService($regis, $regis->account, $vault, $family);
    }

    private function executeService(User $author, Account $account, Vault $vault, Family $family): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'family_id' => $family->id,
            'name' => 'love',
        ];

        $family = (new UpdateFamily)->execute($request);

        $this->assertDatabaseHas('families', [
            'id' => $family->id,
            'name' => 'love',
        ]);
    }
}
