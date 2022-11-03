<?php

namespace Tests\Unit\Helpers;

use App\Helpers\SubscriptionHelper;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Vault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubscriptionHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_checks_if_the_account_has_limitations(): void
    {
        config(['monica.requires_subscription' => true]);

        $account = Account::factory()->create();
        $this->assertTrue(SubscriptionHelper::hasLimitations($account));

        $vault = Vault::factory()->create([
            'account_id' => $account->id,
        ]);
        Contact::factory()->count(6)->create([
            'vault_id' => $vault->id,
        ]);
        $this->assertTrue(SubscriptionHelper::hasLimitations($account));
    }

    /** @test */
    public function it_checks_if_the_account_has_reached_contact_limits(): void
    {
        $account = Account::factory()->create();
        $vault = Vault::factory()->create([
            'account_id' => $account->id,
        ]);
        Contact::factory()->count(3)->create([
            'vault_id' => $vault->id,
        ]);
        $this->assertFalse(SubscriptionHelper::hasReachedContactLimit($account));

        Contact::factory()->count(3)->create([
            'vault_id' => $vault->id,
        ]);
        $this->assertTrue(SubscriptionHelper::hasReachedContactLimit($account));
    }
}
