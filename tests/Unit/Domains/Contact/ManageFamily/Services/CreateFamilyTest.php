<?php

namespace Tests\Unit\Domains\Contact\ManageFamily\Services;

use App\Contact\ManageFamily\Services\CreateFamily;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateFamilyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_family(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);

        $this->executeService($regis, $regis->account, $vault);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateFamily)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);

        $this->executeService($regis, $account, $vault);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_initial_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);

        $this->executeService($regis, $regis->account, $vault);
    }

    private function executeService(User $author, Account $account, Vault $vault): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'name' => 'test',
        ];

        $family = (new CreateFamily)->execute($request);

        $this->assertDatabaseHas('families', [
            'id' => $family->id,
            'name' => 'test',
        ]);
    }
}
