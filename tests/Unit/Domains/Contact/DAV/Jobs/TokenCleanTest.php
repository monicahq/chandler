<?php

namespace Tests\Unit\Domains\Contact\DAV\Jobs;

use App\Domains\Contact\Dav\Jobs\SyncTokenClean;
use App\Models\Account;
use App\Models\SyncToken;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TokenCleanTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function tokenclean_left_one_token()
    {
        $account = Account::factory()->create();
        $user = User::factory()->create([
            'account_id' => $account->id,
        ]);

        SyncToken::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'contacts',
            'timestamp' => now(),
        ]);
        app(SyncTokenClean::class)->execute([]);

        $this->assertDatabaseHas('synctoken', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'contacts',
        ]);
    }

    /** @test */
    public function tokenclean_left_all_token()
    {
        $account = Account::factory()->create();
        $user = User::factory()->create([
            'account_id' => $account->id,
        ]);

        $s1 = SyncToken::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'contacts',
            'timestamp' => now(),
        ]);
        $s2 = SyncToken::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'contacts',
            'timestamp' => now()->addDays(-10),
        ]);
        $s3 = SyncToken::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'contacts',
            'timestamp' => now()->addDays(-15),
        ]);
        $s4 = SyncToken::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'name' => 'contacts',
            'timestamp' => now()->addDays(-1),
        ]);
        app(SyncTokenClean::class)->execute([]);

        $this->assertDatabaseHas('synctoken', [
            'id' => $s1->id,
        ]);
        $this->assertDatabaseMissing('synctoken', [
            'id' => $s2->id,
        ]);
        $this->assertDatabaseMissing('synctoken', [
            'id' => $s3->id,
        ]);
        $this->assertDatabaseHas('synctoken', [
            'id' => $s4->id,
        ]);
    }
}
