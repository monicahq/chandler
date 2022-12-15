<?php

namespace Tests\Unit\Domains\Vault\ManageJournals\Web\ViewHelpers;

use App\Domains\Vault\ManageJournals\Web\ViewHelpers\SliceOfLifeIndexViewHelper;
use App\Models\Journal;
use App\Models\SliceOfLife;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SliceOfLifeIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $user = User::factory()->create();
        $vault = Vault::factory()->create();
        $journal = Journal::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $slice = SliceOfLife::factory()->create([
            'journal_id' => $journal->id,
            'name' => 'this is a title',
        ]);

        $array = SliceOfLifeIndexViewHelper::data($journal);

        $this->assertCount(1, $array);
        $this->assertEquals(
            [
                0 => [
                    'id' => $slice->id,
                    'name' => 'this is a title',
                ],
            ],
            $array['slicesOfLife']->toArray()
        );
    }
}
