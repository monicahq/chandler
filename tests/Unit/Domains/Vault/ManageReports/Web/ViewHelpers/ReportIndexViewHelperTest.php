<?php

namespace Tests\Unit\Domains\Vault\ManageReports\Web\ViewHelpers;

use App\Domains\Vault\ManageReports\Web\ViewHelpers\ReportImportantDateSummaryIndexViewHelper;
use App\Domains\Vault\ManageReports\Web\ViewHelpers\ReportIndexViewHelper;
use App\Models\Contact;
use App\Models\ContactImportantDate;
use App\Models\User;
use App\Models\Vault;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $vault = Vault::factory()->create();
        $array = ReportIndexViewHelper::data($vault);

        $this->assertEquals(
            [
                'url' => [
                    'mood_tracking_events' => env('APP_URL').'/vaults/'.$vault->id.'/reports/moodTrackingEvents',
                    'important_date_summary' => env('APP_URL').'/vaults/'.$vault->id. '/reports/importantDates',
                ]
            ],
            $array
        );
    }
}
