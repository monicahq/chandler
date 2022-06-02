<?php

namespace Tests\Unit\Domains\Contact\ManageGoals\Services;

use App\Contact\ManageGoals\Services\LogStreakForGoal;
use App\Exceptions\EntryAlreadyExistException;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\Contact;
use App\Models\ContactInformationType;
use App\Models\Goal;
use App\Models\Streak;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LogStreakForGoalTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_fails_if_the_streak_already_exists_for_the_date(): void
    {
        $this->expectException(EntryAlreadyExistException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $goal = Goal::factory()->create([
            'contact_id' => $contact->id,
        ]);
        Streak::factory()->create([
            'goal_id' => $goal->id,
            'happened_at' => '1900-01-01 00:00:00',
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $goal);
    }

    /** @test */
    public function it_creates_a_streak(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $goal = Goal::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $goal);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new LogStreakForGoal)->execute($request);
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
        $goal = Goal::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $account, $vault, $contact, $goal);
    }

    /** @test */
    public function it_fails_if_contact_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create();
        $goal = Goal::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $goal);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_initial_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $goal = Goal::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $goal);
    }

    /** @test */
    public function it_fails_if_goal_is_not_in_the_contact(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $goal = ContactInformationType::factory()->create();
        $goal = Goal::factory()->create([
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $goal);
    }

    private function executeService(User $author, Account $account, Vault $vault, Contact $contact, Goal $goal): void
    {
        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'contact_id' => $contact->id,
            'goal_id' => $goal->id,
            'happened_at' => '1900-01-01',
        ];

        (new LogStreakForGoal)->execute($request);

        $this->assertDatabaseHas('streaks', [
            'goal_id' => $goal->id,
            'happened_at' => '1900-01-01 00:00:00',
        ]);
    }
}
