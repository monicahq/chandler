<?php

namespace Tests\Unit\Domains\Contact\DAV\Services;

use App\Domains\Contact\Dav\Services\ImportVCard;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\PHPUnitAssertions;
use Sabre\VObject\Reader;
use Tests\TestCase;

class ImportVCardTest extends TestCase
{
    use DatabaseTransactions,
        PHPUnitAssertions;

    /** @test */
    public function it_can_not_import_because_no_firstname_or_nickname_in_vcard()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([]);

        $this->assertFalse($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_can_not_import_because_no_firstname_in_vcard()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'N' => ['John', '', '', '', ''],
        ]);

        $this->assertFalse($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_can_not_import_because_empty_firstname_in_vcard()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'N' => ';;;;',
        ]);

        $this->assertFalse($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_can_not_import_vcard()
    {
        $importVCard = new ImportVCard;

        $vcard = Reader::read('
BEGIN:VCARD
VERSION:3.0
N:;;;;
FN:
ORG:;
EMAIL;TYPE=home;TYPE=pref:mail@example.org
NOTE:
NICKNAME:
TITLE:
REV:20210900T000102Z
END:VCARD', Reader::OPTION_FORGIVING + Reader::OPTION_IGNORE_INVALID_LINES);

        $this->assertFalse($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_can_not_import_because_empty_nickname_in_vcard()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'NICKNAME' => '',
        ]);

        $this->assertFalse($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_can_not_import_because_empty_fullname_in_vcard()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'FN' => '',
        ]);

        $this->assertFalse($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_can_import_firstname()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'N' => ['', 'John', '', '', ''],
        ]);

        $this->assertTrue($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_can_import_nickname()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'NICKNAME' => 'John',
        ]);

        $this->assertTrue($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_can_import_fullname()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'FN' => 'John Doe',
        ]);

        $this->assertTrue($this->invokePrivateMethod($importVCard, 'canImportCurrentEntry', [$vcard]));
    }

    /** @test */
    public function it_returns_an_unknown_name_if_no_name_is_in_entry()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'EMAIL' => 'john@',
        ]);

        $this->assertEquals(
            trans('settings.import_vcard_unknown_entry'),
            $this->invokePrivateMethod($importVCard, 'name', [$vcard])
        );
    }

    /** @test */
    public function it_returns_a_name_for_N()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'N' => ['John', 'Doe', '', '', ''],
            'EMAIL' => 'john@doe.com',
        ]);

        $this->assertEquals('Doe John john@doe.com', $this->invokePrivateMethod($importVCard, 'name', [$vcard]));
    }

    /** @test */
    public function it_returns_a_name_for_N_incomplete()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'N' => ['John', 'Doe'],
            'EMAIL' => 'john@doe.com',
        ]);

        $this->assertEquals('Doe John john@doe.com', $this->invokePrivateMethod($importVCard, 'name', [$vcard]));
    }

    /** @test */
    public function it_returns_a_name_for_NICKNAME()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'NICKNAME' => 'John',
            'EMAIL' => 'john@doe.com',
        ]);

        $this->assertEquals('John john@doe.com', $this->invokePrivateMethod($importVCard, 'name', [$vcard]));
    }

    /** @test */
    public function it_returns_a_name_for_FN()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'FN' => 'John Doe',
            'EMAIL' => 'john@doe.com',
        ]);

        $this->assertEquals('John Doe john@doe.com', $this->invokePrivateMethod($importVCard, 'name', [$vcard]));
    }

    /** @test */
    public function it_formats_value()
    {
        $importVCard = new ImportVCard;

        $result = $this->invokePrivateMethod($importVCard, 'formatValue', ['']);
        $this->assertNull($result);

        $result = $this->invokePrivateMethod($importVCard, 'formatValue', ['This is a value']);
        $this->assertEquals(
            'This is a value',
            $result
        );
    }

    /** @test */
    public function it_creates_a_contact()
    {
        $author = User::factory()->create();
        $vault = $this->createVaultUser($author, Vault::PERMISSION_EDIT);
        $importVCard = new ImportVCard;
        $importVCard->accountId = $author->account_id;
        $this->setPrivateValue($importVCard, 'author', $author);
        $this->setPrivateValue($importVCard, 'vault', $vault);

        $vcard = new VCard([
            'N' => ['John', 'Doe', '', '', ''],
            'EMAIL' => 'john@doe.com',
        ]);

        $contact = $this->invokePrivateMethod($importVCard, 'importEntry', [null, $vcard, $vcard->serialize(), null]);

        $this->assertTrue($contact->exists);
    }

    /** @test */
    public function it_imports_names_N()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'N' => ['Doe', 'John', 'Jane', '', ''],
        ]);
        $contact = $this->invokePrivateMethod($importVCard, 'importNames', [[], $vcard]);

        $this->assertEquals('John', $contact['first_name']);
        $this->assertEquals('Doe', $contact['last_name']);
        $this->assertEquals('Jane', $contact['middle_name']);
    }

    /** @test */
    public function it_imports_names_NICKNAME()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'NICKNAME' => 'John',
        ]);
        $contact = $this->invokePrivateMethod($importVCard, 'importNames', [[], $vcard]);

        $this->assertEquals('John', $contact['first_name']);
    }

    /** @test */
    public function it_imports_names_FN()
    {
        $author = User::factory()->create();
        $importVCard = new ImportVCard;
        $this->setPrivateValue($importVCard, 'author', $author);

        $vcard = new VCard([
            'FN' => 'John Doe',
        ]);
        $contact = $this->invokePrivateMethod($importVCard, 'importNames', [[], $vcard]);

        $this->assertEquals('John', $contact['first_name']);
        $this->assertEquals('Doe', $contact['last_name']);
    }

    /** @test */
    public function it_imports_names_FN_last()
    {
        $author = User::factory()->create([
            'name_order' => '%last_name% %first_name%',
        ]);
        $importVCard = new ImportVCard;
        $this->setPrivateValue($importVCard, 'author', $author);

        $vcard = new VCard([
            'FN' => 'John Doe',
        ]);
        $contact = $this->invokePrivateMethod($importVCard, 'importNames', [[], $vcard]);

        $this->assertEquals('Doe', $contact['first_name']);
        $this->assertEquals('John', $contact['last_name']);
    }

    /** @test */
    public function it_imports_names_FN_extra_space()
    {
        $author = User::factory()->create();
        $importVCard = new ImportVCard;
        $this->setPrivateValue($importVCard, 'author', $author);

        $vcard = new VCard([
            'FN' => 'John  Doe',
        ]);
        $contact = $this->invokePrivateMethod($importVCard, 'importNames', [[], $vcard]);

        $this->assertEquals('John', $contact['first_name']);
        $this->assertEquals('Doe', $contact['last_name']);
    }

    /** @test */
    public function it_imports_name_FN()
    {
        $author = User::factory()->create();
        $importVCard = new ImportVCard;
        $this->setPrivateValue($importVCard, 'author', $author);

        $vcard = new VCard([
            'FN' => 'John',
            'N' => 'Mike;;;;',
        ]);
        $contact = $this->invokePrivateMethod($importVCard, 'importNames', [[], $vcard]);

        $this->assertEquals('John', $contact['first_name']);
        $this->assertEquals('', Arr::get($contact, 'last_name'));
    }

    /** @test */
    public function it_imports_name_FN_last()
    {
        $author = User::factory()->create([
            'name_order' => '%last_name% %first_name%',
        ]);
        $importVCard = new ImportVCard;
        $this->setPrivateValue($importVCard, 'author', $author);

        $vcard = new VCard([
            'FN' => 'John',
            'N' => 'Mike;;;;',
        ]);
        $contact = $this->invokePrivateMethod($importVCard, 'importNames', [[], $vcard]);

        $this->assertEquals('John', $contact['last_name']);
        $this->assertEquals('', Arr::get($contact, 'first_name'));
    }

    /** @test */
    public function it_imports_names_FN_multiple()
    {
        $author = User::factory()->create();
        $importVCard = new ImportVCard;
        $this->setPrivateValue($importVCard, 'author', $author);

        $vcard = new VCard([
            'FN' => 'John Doe Marco',
            'N' => 'Mike;;;;',
        ]);
        $contact = $this->invokePrivateMethod($importVCard, 'importNames', [[], $vcard]);

        $this->assertEquals('John', $contact['first_name']);
        $this->assertEquals('Doe Marco', $contact['last_name']);
    }

    /** @test */
    public function it_imports_uuid_default()
    {
        $importVCard = new ImportVCard;

        $vcard = new VCard([
            'UID' => '31fdc242-c974-436e-98de-6b21624d6e34',
        ]);

        $contact = [];

        $contact = $this->invokePrivateMethod($importVCard, 'importUid', [$contact, $vcard]);

        $this->assertEquals('31fdc242-c974-436e-98de-6b21624d6e34', $contact['uuid']);
    }

    /** @test */
    public function it_imports_uuid_contact()
    {
        $author = User::factory()->create();
        $vault = $this->createVaultUser($author, Vault::PERMISSION_EDIT);
        $importVCard = new ImportVCard;
        $importVCard->accountId = $author->account_id;
        $this->setPrivateValue($importVCard, 'author', $author);
        $this->setPrivateValue($importVCard, 'vault', $vault);

        $vcard = new VCard([
            'FN' => 'John Doe',
            'UID' => '31fdc242-c974-436e-98de-6b21624d6e34',
        ]);

        $contact = $this->invokePrivateMethod($importVCard, 'importEntry', [null, $vcard, $vcard->serialize(), null]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'uuid' => '31fdc242-c974-436e-98de-6b21624d6e34',
        ]);
        $this->assertEquals('31fdc242-c974-436e-98de-6b21624d6e34', $contact->uuid);
    }
}
