<?php

namespace Tests\Unit\Controllers\Vault\Contact\Modules\Note\ViewHelpers;

use App\Http\Controllers\Vault\Contact\Modules\ContactName\ViewHelpers\ModuleContactNameViewHelper;
use Illuminate\Support\Str;
use function env;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Note;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\Modules\Note\ViewHelpers\ModuleNotesViewHelper;
use App\Models\User;

class ModuleContactNameViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $array = ModuleContactNameViewHelper::data($contact, $user);

        $this->assertEquals(
            1,
            count($array)
        );

        $this->assertArrayHasKey('name', $array);

        $this->assertEquals(
            [
                'name' => $contact->getName($user),
            ],
            $array
        );
    }
}
