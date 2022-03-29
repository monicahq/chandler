<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Gender;
use App\Models\Module;
use App\Models\Account;
use App\Models\Emotion;
use App\Models\Pronoun;
use App\Models\Template;
use App\Models\GroupType;
use App\Models\AddressType;
use App\Models\PetCategory;
use App\Models\RelationshipGroupType;
use App\Models\ContactInformationType;
use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrencyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_many_accounts(): void
    {
        $account = Account::factory()->create();
        $currency = Currency::factory()->create();

        $currency->accounts()->sync([$account->id => ['active' => true]]);

        $this->assertTrue($currency->accounts()->exists());
    }
}
