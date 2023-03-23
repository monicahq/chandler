<?php

namespace Tests\Unit\Domains\Contact\ManageGifts\Services;

use App\Domains\Contact\ManageGifts\Services\DestroyGift;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\Gift;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DestroyGiftTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_a_gift(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $gift = Gift::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $gift);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyGift())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $gift = Gift::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $account, $vault, $gift);
    }

    /** @test */
    public function it_fails_if_gift_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $gift = Gift::factory()->create();

        $this->executeService($regis, $regis->account, $vault, $gift);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $gift = Gift::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $gift);
    }

    private function executeService(User $author, Account $account, Vault $vault, Gift $gift): void
    {
        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'gift_id' => $gift->id,
        ];

        (new DestroyGift())->execute($request);

        $this->assertDatabaseMissing('gifts', [
            'id' => $gift->id,
        ]);

        $this->assertDatabaseMissing('gift_donators', [
            'gift_id' => $gift->id,
        ]);

        $this->assertDatabaseMissing('gift_recipients', [
            'gift_id' => $gift->id,
        ]);
    }
}
