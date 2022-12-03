<?php

namespace Tests\Unit\Domains\Vault\ManageJournals\Web\ViewHelpers;

use App\Domains\Vault\ManageJournals\Web\ViewHelpers\PostShowViewHelper;
use App\Models\Contact;
use App\Models\Journal;
use App\Models\Post;
use App\Models\PostSection;
use App\Models\Tag;
use App\Models\User;
use App\Models\Vault;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostShowViewHelperTest extends TestCase
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
        $post = Post::factory()->create([
            'journal_id' => $journal->id,
            'title' => 'this is a title',
            'written_at' => '2022-01-01 00:00:00',
        ]);
        $section = PostSection::factory()->create([
            'post_id' => $post->id,
            'label' => 'super',
            'content' => 'this is a content',
        ]);
        $tag = Tag::factory()->create([
            'name' => 'super',
        ]);
        $post->tags()->attach($tag->id);

        $array = PostShowViewHelper::data($post, $user);

        $this->assertCount(9, $array);
        $this->assertEquals(
            $post->id,
            $array['id']
        );
        $this->assertEquals(
            'this is a title',
            $array['title']
        );
        $this->assertTrue(
            $array['title_exists']
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => $section->id,
                    'label' => 'super',
                    'content' => 'this is a content',
                ],
            ],
            $array['sections']->toArray()
        );
        $this->assertEquals(
            [
                0 => [
                    'id' => $tag->id,
                    'name' => 'super',
                ],
            ],
            $array['tags']->toArray()
        );
        $this->assertEquals(
            [
                'name' => $journal->name,
                'url' => [
                    'show' => env('APP_URL').'/vaults/'.$vault->id.'/journals/'.$journal->id,
                ],
            ],
            $array['journal']
        );
        $this->assertEquals(
            [
                'edit' => env('APP_URL').'/vaults/'.$vault->id.'/journals/'.$journal->id.'/posts/'.$post->id.'/edit',
                'back' => env('APP_URL').'/vaults/'.$vault->id.'/journals/'.$journal->id,
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_all_the_groups_for_the_contact(): void
    {
        $contact = Contact::factory()->create([]);

        $array = PostShowViewHelper::dtoContact($contact);

        $this->assertEquals(
            4,
            count($array)
        );
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('avatar', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                'id' => $contact->id,
                'name' => $contact->name,
                'avatar' => $contact->avatar,
                'url' => [
                    'show' => env('APP_URL').'/vaults/'.$contact->vault_id.'/contacts/'.$contact->id,
                ],
            ],
            $array
        );
    }
}
