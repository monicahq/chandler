<?php

namespace Tests\Unit\Services\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Services\User\StoreNameOrderPreference;
use App\Services\User\StoreTimezone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StoreTimezoneTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_stores_the_timezone(): void
    {
        $ross = $this->createUser();
        $this->executeService($ross, '%name%', $ross->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new StoreTimezone)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $this->executeService($ross, '%user%', $account);
    }

    private function executeService(User $author, string $nameOrder, Account $account): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'timezone' => 'UTC',
        ];

        $user = (new StoreTimezone)->execute($request);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'account_id' => $account->id,
            'timezone' => 'UTC',
        ]);

        $this->assertInstanceOf(
            User::class,
            $user
        );
    }
}
