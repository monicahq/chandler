<?php

namespace Tests\Unit\Models;

use App\Models\PostTypeSection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostTypeSectionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_post_type()
    {
        $postTypeSection = PostTypeSection::factory()->create();

        $this->assertTrue($postTypeSection->postType()->exists());
    }
}
