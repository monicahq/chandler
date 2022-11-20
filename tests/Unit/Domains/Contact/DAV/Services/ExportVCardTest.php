<?php

namespace Tests\Unit\Domains\Contact\DAV\Services;

use App\Domains\Contact\Dav\Services\ExportVCard;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\PHPUnitAssertions;
use Tests\TestCase;
use Tests\Unit\Domains\Contact\DAV\CardEtag;

class ExportVCardTest extends TestCase
{
    use DatabaseTransactions,
        PHPUnitAssertions,
        CardEtag;

    /** @var int */
    const defaultPropsCount = 3;

    /** @test */
    // public function vcard_add_names()
    // {
    //     $user = $this->createUser();
    //     $vault = $this->createVaultUser($user);
    //     $contact = Contact::factory()->random()->create(['vault_id' => $vault->id]);

    //     $vCard = new VCard();

    //     $exportVCard = app(ExportVCard::class);
    //     $this->invokePrivateMethod($exportVCard, 'exportNames', [$contact, $vCard]);

    //     $this->assertCount(
    //         self::defaultPropsCount + 2,
    //         $vCard->children()
    //     );
    //     $this->assertStringContainsString("FN:{$contact->name}", $vCard->serialize());
    //     $this->assertStringContainsString("N:{$contact->last_name};{$contact->first_name};;;", $vCard->serialize());
    // }
}
