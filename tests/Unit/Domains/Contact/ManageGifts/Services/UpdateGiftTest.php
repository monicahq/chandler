<?php

namespace Tests\Unit\Domains\Contact\ManageGifts\Services;

use App\Domains\Contact\ManageGifts\Services\UpdateGift;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\Gift;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateGiftTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_gift(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $donator = Contact::factory()->create(['vault_id' => $vault->id]);
        $recipient = Contact::factory()->create(['vault_id' => $vault->id]);
        $gift = Gift::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $donator, $recipient, $gift);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateGift())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $donator = Contact::factory()->create(['vault_id' => $vault->id]);
        $recipient = Contact::factory()->create(['vault_id' => $vault->id]);
        $gift = Gift::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $account, $vault, $donator, $recipient, $gift);
    }

    /** @test */
    public function it_fails_if_gift_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $donator = Contact::factory()->create(['vault_id' => $vault->id]);
        $recipient = Contact::factory()->create(['vault_id' => $vault->id]);
        $gift = Gift::factory()->create();

        $this->executeService($regis, $regis->account, $vault, $donator, $recipient, $gift);
    }

    /** @test */
    public function it_fails_if_recipient_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $donator = Contact::factory()->create();
        $recipient = Contact::factory()->create(['vault_id' => $vault->id]);
        $gift = Gift::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $donator, $recipient, $gift);
    }

    /** @test */
    public function it_fails_if_donator_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $donator = Contact::factory()->create(['vault_id' => $vault->id]);
        $recipient = Contact::factory()->create();
        $gift = Gift::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $donator, $recipient, $gift);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $donator = Contact::factory()->create(['vault_id' => $vault->id]);
        $recipient = Contact::factory()->create(['vault_id' => $vault->id]);
        $gift = Gift::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $donator, $recipient, $gift);
    }

    private function executeService(User $author, Account $account, Vault $vault, Contact $donator, Contact $recipient, Gift $gift): void
    {
        $currency = Currency::factory()->create();

        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'currency_id' => $currency->id,
            'gift_id' => $gift->id,
            'name' => 'gift name',
            'description' => 'This is incredible',
            'budget' => 123,
            'donators_ids' => [$donator->id],
            'recipients_ids' => [$recipient->id],
        ];

        $gift = (new UpdateGift())->execute($request);

        $this->assertDatabaseHas('gifts', [
            'id' => $gift->id,
            'vault_id' => $vault->id,
            'currency_id' => $currency->id,
            'gift_occasion_id' => null,
            'gift_state_id' => null,
            'name' => 'gift name',
            'description' => 'This is incredible',
            'budget' => 123,
        ]);

        $this->assertDatabaseHas('gift_donators', [
            'gift_id' => $gift->id,
            'contact_id' => $donator->id,
        ]);

        $this->assertDatabaseHas('gift_recipients', [
            'gift_id' => $gift->id,
            'contact_id' => $recipient->id,
        ]);
    }
}
