<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\CallReason;
use App\Models\CallReasonType;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactImportantDateType;
use App\Models\Label;
use App\Models\Template;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CallReasonTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_an_call_reason_type()
    {
        $callReason = CallReason::factory()->create();
        $this->assertTrue($callReason->callReasonType()->exists());
    }
}
