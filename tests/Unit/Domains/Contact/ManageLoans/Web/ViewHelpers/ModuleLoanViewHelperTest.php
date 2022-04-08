<?php

namespace Tests\Unit\Domains\Contact\ManageLoans\Web\ViewHelpers;

use function env;

use App\Contact\ManageLoans\Web\ViewHelpers\ModuleLoanViewHelper;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Contact;
use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ModuleLoanViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $currency = Currency::factory()->create();
        $user->account->currencies()->syncWithoutDetaching([$currency->id => ['active' => true]]);

        Loan::factory()->create([
            'contact_id' => $contact->id,
        ]);
        $array = ModuleLoanViewHelper::data($contact, $user);

        $this->assertEquals(
            3,
            count($array)
        );

        $this->assertArrayHasKey('loans', $array);
        $this->assertArrayHasKey('currencies', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                'id' => 165,
                'name' => 'CAD',
            ],
            $array['currencies']->toArray()[0]
        );

        $this->assertEquals(
            [
                'store' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/loans',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_transfer_object(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $contact = Contact::factory()->create();
        $otherContact = Contact::factory()->create();
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            'contact_id' => $contact->id,
        ]);
        $contact->loanAsLoaner()->syncWithoutDetaching([$loan->id => ['loanee_id' => $otherContact->id]]);

        $array = ModuleLoanViewHelper::dtoLoan($loan, $contact, $user);

        $this->assertEquals(
            8,
            count($array)
        );

        $this->assertEquals(
            $loan->id,
            $array['id']
        );
        $this->assertEquals(
            $loan->type,
            $array['type']
        );
        $this->assertEquals(
            $loan->name,
            $array['name']
        );
        $this->assertEquals(
            $loan->description,
            $array['description']
        );
        $this->assertEquals(
            $loan->amount_lent,
            $array['amount_lent']
        );
        $this->assertEquals(
            $loan->amount_lent,
            $array['amount_lent']
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => $contact->id,
                    'name' => $contact->getName($user),
                ],
            ],
            $array['loaners']->toArray()
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => $otherContact->id,
                    'name' => $otherContact->getName($user),
                ],
            ],
            $array['loanees']->toArray()
        );
        $this->assertEquals(
            [
                'update' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/loans/'.$loan->id,
                'destroy' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/loans/'.$loan->id,
            ],
            $array['url']
        );

        // $this->assertEquals(
        //     [
        //         'id' => $loan->id,
        //         'type' => $loan->type,
        //         'name' => $loan->name,
        //         'description' => $loan->description,
        //         'amount_lent' => $loan->amount_lent,
        //         'loaners' => $loanersCollection,
        //         'loanees' => $loaneesCollection,
        //         'url' => [
        //             'update' => env('APP_URL') . '/vaults/' . $contact->vault->id . '/contacts/' . $contact->id . '/notes/' . $note->id,
        //             'destroy' => env('APP_URL') . '/vaults/' . $contact->vault->id . '/contacts/' . $contact->id . '/notes/' . $note->id,
        //         ],
        //     ],
        //     $array
        // );
    }
}
