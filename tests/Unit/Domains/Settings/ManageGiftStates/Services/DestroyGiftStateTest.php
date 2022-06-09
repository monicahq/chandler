<?php

namespace Tests\Unit\Domains\Settings\ManageGiftStates\Services;

use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\GiftOccasion;
use App\Models\GiftState;
use App\Models\User;
use App\Settings\ManageGiftOccasions\Services\DestroyGiftOccasion;
use App\Settings\ManageGiftStages\Services\DestroyGiftStage;
use App\Settings\ManageGiftStates\Services\DestroyGiftState;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DestroyGiftStateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_a_gift_state(): void
    {
        $ross = $this->createAdministrator();
        $giftStage = GiftState::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $ross->account, $giftStage);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyGiftState)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = Account::factory()->create();
        $giftStage = GiftState::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $account, $giftStage);
    }

    /** @test */
    public function it_fails_if_type_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $giftStage = GiftState::factory()->create();
        $this->executeService($ross, $ross->account, $giftStage);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $giftStage = GiftState::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $ross->account, $giftStage);
    }

    private function executeService(User $author, Account $account, GiftState $giftStage): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'gift_state_id' => $giftStage->id,
        ];

        (new DestroyGiftState)->execute($request);

        $this->assertDatabaseMissing('gift_states', [
            'id' => $giftStage->id,
        ]);
    }
}
