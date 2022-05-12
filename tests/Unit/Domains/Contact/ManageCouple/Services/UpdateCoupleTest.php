<?php

namespace Tests\Unit\Domains\Contact\ManageCouple\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vault;
use App\Models\Couple;
use App\Models\Account;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use App\Contact\ManageCouple\Services\UpdateCouple;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateCoupleTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_couple(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $couple = Couple::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $couple);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateCouple)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $couple = Couple::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $account, $vault, $couple);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_initial_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $couple = Couple::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $couple);
    }

    /** @test */
    public function it_fails_if_vault_is_not_in_the_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $couple = Couple::factory()->create();

        $this->executeService($regis, $regis->account, $vault, $couple);
    }

    private function executeService(User $author, Account $account, Vault $vault, Couple $couple): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'couple_id' => $couple->id,
            'name' => 'love',
        ];

        $couple = (new UpdateCouple)->execute($request);

        $this->assertDatabaseHas('couples', [
            'id' => $couple->id,
            'name' => 'love',
        ]);
    }
}
