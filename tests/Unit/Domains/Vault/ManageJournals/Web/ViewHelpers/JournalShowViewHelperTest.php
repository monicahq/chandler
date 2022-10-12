<?php

namespace Tests\Unit\Domains\Vault\ManageJournals\Web\ViewHelpers;

use App\Models\Journal;
use App\Models\Post;
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
        Post::factory()->create([
            'journal_id' => $journal->id,
            'title' => 'My Post',
            'written_at' => '2020-01-01',
        ]);

        $array = JournalShowViewHelper::data($journal, 2020, $user);
        $this->assertCount(6, $array);
        $this->assertEquals(
            $journal->id,
            $array['id']
        );
        $this->assertEquals(
            'My Journal',
            $array['name']
        );
        $this->assertEquals(
            'My Journal Description',
            $array['description']
        );
        $this->assertEquals(
            [
                'create' => env('APP_URL').'/vaults/'.$journal->vault->id.'/journals/'.$journal->id.'/posts/create',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_posts_in_the_given_year(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'name' => 'My Journal',
            'description' => 'My Journal Description',
        ]);
        $post = Post::factory()->create([
            'journal_id' => $journal->id,
            'title' => 'My Post',
            'written_at' => '2020-01-01',
        ]);

        $collection = JournalShowViewHelper::postsInYear($journal, 2020, $user);

        $this->assertEquals(
            'January 2020',
            $collection->toArray()[0]['month']
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => $post->id,
                    'title' => 'My Post',
                    'excerpt' => null,
                    'written_at_day' => 'WED',
                    'written_at_day_number' => '01',
                    'url' => [
                        'show' => env('APP_URL').'/vaults/'.$journal->vault->id.'/journals/'.$journal->id.'/posts/'.$post->id,
                    ],
                ],
            ],
            $collection->toArray()[0]['posts']->toArray()
        );
    }

    /** @test */
    public function it_gets_the_years_in_the_given_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'name' => 'My Journal',
            'description' => 'My Journal Description',
        ]);
        $post = Post::factory()->create([
            'journal_id' => $journal->id,
            'title' => 'My Post',
            'written_at' => '2020-01-01',
        ]);

        $collection = JournalShowViewHelper::yearsOfContentInJournal($journal);

        $this->assertEquals(
            [
                0 => [
                    'year' => 2020,
                    'posts' => 1,
                    'url' => [
                        'show' => env('APP_URL').'/vaults/'.$journal->vault->id.'/journals/'.$journal->id.'/years/2020',
                    ],
                ],
            ],
            $collection->toArray()
        );
    }
}
