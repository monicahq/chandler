<?php

namespace Tests\Unit\Domains\Contact\ManageLifeEvents\Services;

use App\Domains\Contact\ManageLifeEvents\Services\DestroyTimelineEvent;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\LifeEvent;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\TimelineEvent;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DestroyTimelineEventTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_a_timeline_event(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault($user->account);
        $vault = $this->setPermissionInVault($user, Vault::PERMISSION_EDIT, $vault);
        $timelineEvent = TimelineEvent::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($user, $user->account, $vault, $timelineEvent);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'summary' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyTimelineEvent())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($user->account);
        $vault = $this->setPermissionInVault($user, Vault::PERMISSION_EDIT, $vault);
        $timelineEvent = TimelineEvent::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($user, $account, $vault, $timelineEvent);
    }

    /** @test */
    public function it_fails_if_vault_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($account);
        $vault = $this->setPermissionInVault($user, Vault::PERMISSION_EDIT, $vault);
        $timelineEvent = TimelineEvent::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($user, $user->account, $vault, $timelineEvent);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_initial_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $user = $this->createUser();
        $vault = $this->createVault($user->account);
        $vault = $this->setPermissionInVault($user, Vault::PERMISSION_VIEW, $vault);
        $timelineEvent = TimelineEvent::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->executeService($user, $user->account, $vault, $timelineEvent);
    }

    /** @test */
    public function it_fails_if_timeline_event_does_not_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault($user->account);
        $vault = $this->setPermissionInVault($user, Vault::PERMISSION_EDIT, $vault);
        $timelineEvent = TimelineEvent::factory()->create([]);

        $this->executeService($user, $user->account, $vault, $timelineEvent);
    }

    private function executeService(User $user, Account $account, Vault $vault, TimelineEvent $timelineEvent): void
    {
        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $user->id,
            'timeline_event_id' => $timelineEvent->id,
        ];

        (new DestroyTimelineEvent)->execute($request);

        $this->assertDatabaseMissing('timeline_events', [
            'id' => $timelineEvent->id,
        ]);
    }
}
