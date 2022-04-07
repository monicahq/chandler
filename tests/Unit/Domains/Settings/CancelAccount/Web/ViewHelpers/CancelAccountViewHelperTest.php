<?php

namespace Tests\Unit\Domains\Settings\CancelAccount\Web\ViewHelpers;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Models\ContactImportantDate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\ImportantDates\ViewHelpers\ContactImportantDatesViewHelper;
use App\Settings\CancelAccount\Web\ViewHelpers\CancelAccountViewHelper;

class CancelAccountViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $array = CancelAccountViewHelper::data();

        $this->assertEquals(
            [
                'settings' => env('APP_URL') . '/settings',
                'back' => env('APP_URL') . '/settings',
                'destroy' => env('APP_URL') . '/settings/cancel',
            ],
            $array['url']
        );
    }
}
