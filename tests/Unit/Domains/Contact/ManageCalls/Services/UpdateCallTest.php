<?php

namespace Tests\Unit\Domains\Contact\ManageCalls\Services;

use App\Contact\ManageCalls\Services\UpdateCall;
use App\Contact\ManageTasks\Services\UpdateContactTask;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\Call;
use App\Models\Contact;
use App\Models\ContactInformationType;
use App\Models\ContactTask;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateCallTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_task(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $call = Call::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $call);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateCall())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $call = Call::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $account, $vault, $contact, $call);
    }

    /** @test */
    public function it_fails_if_contact_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create();
        $call = Call::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $call);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $call = Call::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $call);
    }

    /** @test */
    public function it_fails_if_reminder_is_not_in_the_contact(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $call = Call::factory()->create();

        $this->executeService($regis, $regis->account, $vault, $contact, $call);
    }

    private function executeService(User $author, Account $account, Vault $vault, Contact $contact, Call $call): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'contact_id' => $contact->id,
            'call_id' => $call->id,
            'called_at' => '1999-01-01',
            'duration' => 100,
            'type' => 'audio',
            'answered' => true,
            'who_initiated' => 'contact',
        ];

        $call = (new UpdateCall())->execute($request);

        $this->assertDatabaseHas('calls', [
            'id' => $call->id,
            'contact_id' => $contact->id,
            'called_at' => '1999-01-01 00:00:00',
            'duration' => 100,
            'type' => 'audio',
            'answered' => true,
            'who_initiated' => 'contact',
        ]);
    }
}
