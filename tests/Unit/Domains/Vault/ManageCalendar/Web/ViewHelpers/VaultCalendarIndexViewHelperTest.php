<?php

namespace Tests\Unit\Domains\Vault\ManageCalendar\Web\ViewHelpers;

use App\Domains\Vault\ManageCalendar\Web\ViewHelpers\VaultCalendarIndexViewHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VaultCalendarIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
    }

    /** @test */
    public function it_builds_the_monthly_calendar(): void
    {
        dd(VaultCalendarIndexViewHelper::buildMonth(6));
    }
}
