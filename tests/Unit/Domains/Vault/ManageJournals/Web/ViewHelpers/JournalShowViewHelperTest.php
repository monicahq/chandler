<?php

namespace Tests\Unit\Domains\Vault\ManageJournals\Web\ViewHelpers;

use App\Models\Journal;
use App\Models\User;
use App\Vault\ManageJournals\Web\ViewHelpers\JournalShowViewHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JournalShowViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'name' => 'My Journal',
            'description' => 'My Journal Description',
        ]);

        $array = JournalShowViewHelper::data($journal, $user);
        $this->assertEquals(
            [
                'id' => $journal->id,
                'name' => 'My Journal',
                'description' => 'My Journal Description',
            ],
            $array
        );
    }
}
