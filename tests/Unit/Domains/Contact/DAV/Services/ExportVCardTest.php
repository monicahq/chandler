<?php

namespace Tests\Unit\Domains\Contact\DAV\Services;

use App\Domains\Contact\Dav\Services\ExportVCard;
use App\Models\Contact;
use App\Models\Gender;
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
    public function vcard_add_names()
    {
        $user = $this->createUser();
        $vault = $this->createVaultUser($user);
        $contact = Contact::factory()->random()->create(['vault_id' => $vault->id]);

        $vCard = new VCard();

        $exportVCard = app(ExportVCard::class);
        $this->invokePrivateMethod($exportVCard, 'exportNames', [$contact, $vCard]);

        $this->assertCount(
            self::defaultPropsCount + 2,
            $vCard->children()
        );
        $this->assertStringContainsString("FN:{$contact->name}", $vCard->serialize());
        $this->assertStringContainsString("N:{$contact->last_name};{$contact->first_name};;;", $vCard->serialize());
    }

    /** @test */
    public function vcard_add_nickname()
    {
        $user = $this->createUser();
        $vault = $this->createVaultUser($user);
        $contact = Contact::factory()->random()->nickname()->create([
            'vault_id' => $vault->id,
        ]);

        $vCard = new VCard();

        $exportVCard = app(ExportVCard::class);
        $this->invokePrivateMethod($exportVCard, 'exportNames', [$contact, $vCard]);

        $this->assertCount(
            self::defaultPropsCount + 3,
            $vCard->children()
        );
        $this->assertStringContainsString("FN:{$contact->name}", $vCard->serialize());
        $this->assertStringContainsString("N:{$contact->last_name};{$contact->first_name};;;", $vCard->serialize());
        $this->assertStringContainsString("NICKNAME:{$contact->nickname}", $vCard->serialize());
    }

    /** @test */
    public function vcard_add_gender()
    {
        $user = $this->createUser();
        $vault = $this->createVaultUser($user);
        $contact = Contact::factory()->random()->create(['vault_id' => $vault->id]);

        $vCard = new VCard();

        $exportVCard = app(ExportVCard::class);
        $this->invokePrivateMethod($exportVCard, 'exportGender', [$contact, $vCard]);

        $this->assertCount(
            self::defaultPropsCount + 1,
            $vCard->children()
        );
        $this->assertStringContainsString("GENDER:{$contact->gender->type}", $vCard->serialize());
    }

    /** @test */
    public function vcard_add_gender_female()
    {
        $user = $this->createUser();
        $vault = $this->createVaultUser($user);
        $gender = Gender::factory()->create([
            'account_id' => $user->account_id,
            'type' => 'F',
            'name' => 'Female',
        ]);
        $contact = Contact::factory()->random()->create([
            'vault_id' => $vault->id,
            'gender_id' => $gender->id,
        ]);

        $vCard = new VCard();

        $exportVCard = new ExportVCard();
        $this->invokePrivateMethod($exportVCard, 'exportGender', [$contact, $vCard]);

        $this->assertCount(
            self::defaultPropsCount + 1,
            $vCard->children()
        );
        $this->assertStringContainsString('GENDER:F', $vCard->serialize());
    }

    /** @test */
    public function vcard_add_gender_unknown()
    {
        $user = $this->createUser();
        $vault = $this->createVaultUser($user);
        $gender = Gender::factory()->create([
            'account_id' => $user->account_id,
            'type' => 'U',
        ]);
        $contact = Contact::factory()->random()->create([
            'vault_id' => $vault->id,
            'gender_id' => $gender->id,
        ]);

        $vCard = new VCard();

        $exportVCard = new ExportVCard();
        $this->invokePrivateMethod($exportVCard, 'exportGender', [$contact, $vCard]);

        $this->assertCount(
            self::defaultPropsCount + 1,
            $vCard->children()
        );
        $this->assertStringContainsString('GENDER:U', $vCard->serialize());
    }

    /** @test */
    public function vcard_add_gender_type_null()
    {
        $user = $this->createUser();
        $vault = $this->createVaultUser($user);
        $gender = Gender::factory()->create([
            'account_id' => $user->account_id,
            'type' => null,
            'name' => 'Something',
        ]);
        $contact = Contact::factory()->random()->create([
            'vault_id' => $vault->id,
            'gender_id' => $gender->id,
        ]);

        $vCard = new VCard();

        $exportVCard = new ExportVCard();
        $this->invokePrivateMethod($exportVCard, 'exportGender', [$contact, $vCard]);

        $this->assertCount(
            self::defaultPropsCount + 1,
            $vCard->children()
        );
        $this->assertStringContainsString('GENDER:O', $vCard->serialize());
    }

    /** @test */
    public function vcard_add_gender_type_null_male()
    {
        $user = $this->createUser();
        $vault = $this->createVaultUser($user);
        $gender = Gender::factory()->create([
            'account_id' => $user->account_id,
            'type' => null,
            'name' => 'Male',
        ]);
        $contact = Contact::factory()->random()->create([
            'vault_id' => $vault->id,
            'gender_id' => $gender->id,
        ]);

        $vCard = new VCard();

        $exportVCard = new ExportVCard();
        $this->invokePrivateMethod($exportVCard, 'exportGender', [$contact, $vCard]);

        $this->assertCount(
            self::defaultPropsCount + 1,
            $vCard->children()
        );
        $this->assertStringContainsString('GENDER:M', $vCard->serialize());
    }

    /** @test */
    public function vcard_add_gender_type_null_female()
    {
        $user = $this->createUser();
        $vault = $this->createVaultUser($user);
        $gender = Gender::factory()->create([
            'account_id' => $user->account_id,
            'type' => null,
            'name' => 'Female',
        ]);
        $contact = Contact::factory()->random()->create([
            'vault_id' => $vault->id,
            'gender_id' => $gender->id,
        ]);

        $vCard = new VCard();

        $exportVCard = new ExportVCard();
        $this->invokePrivateMethod($exportVCard, 'exportGender', [$contact, $vCard]);

        $this->assertCount(
            self::defaultPropsCount + 1,
            $vCard->children()
        );
        $this->assertStringContainsString('GENDER:F', $vCard->serialize());
    }

    /** @test */
    // public function vcard_add_photo()
    // {
    //     $user = $this->createUser();
    //     $vault = $this->createVaultUser($user);
    //     $contact = Contact::factory()->random()->create(['vault_id' => $vault->id]);

    //     $vCard = new VCard();

    //     $contact->avatar_source = 'gravatar';
    //     $contact->avatar_gravatar_url = 'gravatar';

    //     $exportVCard = app(ExportVCard::class);
    //     $this->invokePrivateMethod($exportVCard, 'exportPhoto', [$contact, $vCard]);

    //     $this->assertCount(
    //         self::defaultPropsCount + 1,
    //         $vCard->children()
    //     );
    //     $this->assertStringContainsString('PHOTO;VALUE=URI:gravatar', $vCard->serialize());
    // }
}
