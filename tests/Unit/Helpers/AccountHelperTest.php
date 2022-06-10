<?php

namespace Tests\Unit\Helpers;

use App\Helpers\AccountHelper;
use App\Helpers\DateHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_generates_a_short_code(): void
    {
        $shortCode = AccountHelper::generateShortCode();
        $this->assertEquals(
            8,
            strlen($shortCode)
        );
    }
}
