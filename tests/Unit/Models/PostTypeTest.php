<?php

namespace Tests\Unit\Models;

use App\Models\PostType;
use App\Models\PostTypeSection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostTypeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_account()
    {
        $postType = PostType::factory()->create();

        $this->assertTrue($postType->account()->exists());
    }

    /** @test */
    public function it_has_many_sections(): void
    {
        $postType = PostType::factory()->create();

        PostTypeSection::factory()->create([
            'post_type_id' => $postType->id,
        ]);

        $this->assertTrue($postType->postTypeSections()->exists());
    }
}
