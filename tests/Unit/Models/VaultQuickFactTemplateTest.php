<?php

namespace Tests\Unit\Models;

use App\Models\Contact;
use App\Models\Label;
use App\Models\VaultQuickFactTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VaultQuickFactTemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_vault()
    {
        $quickFactTemplate = VaultQuickFactTemplate::factory()->create();

        $this->assertTrue($quickFactTemplate->vault()->exists());
    }
}
