<?php

namespace Tests\Unit\Domains\Settings\ManageGiftStates\Services;

use App\Models\Account;
use App\Models\GiftOccasion;
use App\Models\GiftState;
use App\Models\User;
use App\Settings\ManageGiftOccasions\Services\UpdateGiftOccasionPosition;
use App\Settings\ManageGiftStages\Services\UpdateGiftStagePosition;
use App\Settings\ManageGiftStates\Services\UpdateGiftStatePosition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateGiftStatePositionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_state_position(): void
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
        (new UpdateGiftStatePosition)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $giftStage = GiftState::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $account, $giftStage);
    }

    /** @test */
    public function it_fails_if_gift_stage_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $giftStage = GiftState::factory()->create();
        $this->executeService($ross, $ross->account, $giftStage);
    }

    private function executeService(User $author, Account $account, GiftState $giftStage): void
    {
        $giftStage1 = GiftState::factory()->create([
            'account_id' => $account->id,
            'position' => 1,
        ]);
        $giftStage3 = GiftState::factory()->create([
            'account_id' => $account->id,
            'position' => 3,
        ]);
        $giftStage4 = GiftState::factory()->create([
            'account_id' => $account->id,
            'position' => 4,
        ]);

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'gift_state_id' => $giftStage->id,
            'new_position' => 3,
        ];

        $giftStage = (new UpdateGiftStatePosition)->execute($request);

        $this->assertDatabaseHas('gift_states', [
            'id' => $giftStage1->id,
            'account_id' => $account->id,
            'position' => 1,
        ]);
        $this->assertDatabaseHas('gift_states', [
            'id' => $giftStage3->id,
            'account_id' => $account->id,
            'position' => 2,
        ]);
        $this->assertDatabaseHas('gift_states', [
            'id' => $giftStage4->id,
            'account_id' => $account->id,
            'position' => 4,
        ]);
        $this->assertDatabaseHas('gift_states', [
            'id' => $giftStage->id,
            'account_id' => $account->id,
            'position' => 3,
        ]);

        $this->assertInstanceOf(
            GiftState::class,
            $giftStage
        );
    }
}
