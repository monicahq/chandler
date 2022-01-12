<?php

namespace Tests\Unit\Controllers\Vault\Contact\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\Vault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactCreateViewHelper;

class ContactCreateViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $vault = Vault::factory()->create();
        $array = ContactCreateViewHelper::data($vault);
        $this->assertEquals(
            [
                'url' => [
                    'store' => env('APP_URL').'/vaults/'.$vault->id.'/contacts',
                    'back' => env('APP_URL').'/vaults/'.$vault->id.'/contacts',
                ],
            ],
            $array
        );
    }
}
