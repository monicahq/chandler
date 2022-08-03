<?php

namespace Tests\Unit\Models;

use App\Models\Address;
use App\Models\Avatar;
use App\Models\Call;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactImportantDate;
use App\Models\ContactImportantDateType;
use App\Models\ContactInformation;
use App\Models\ContactLog;
use App\Models\ContactReminder;
use App\Models\ContactTask;
use App\Models\File;
use App\Models\Gender;
use App\Models\Goal;
use App\Models\Group;
use App\Models\Label;
use App\Models\Loan;
use App\Models\Note;
use App\Models\Pet;
use App\Models\Pronoun;
use App\Models\RelationshipType;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_vault()
    {
        $contact = Contact::factory()->create();

        $this->assertTrue($contact->vault()->exists());
    }

    /** @test */
    public function it_has_one_gender()
    {
        $contact = Contact::factory()->create([
            'gender_id' => Gender::factory()->create(),
        ]);

        $this->assertTrue($contact->gender()->exists());
    }

    /** @test */
    public function it_has_one_pronoun()
    {
        $contact = Contact::factory()->create([
            'pronoun_id' => Pronoun::factory()->create(),
        ]);

        $this->assertTrue($contact->pronoun()->exists());
    }

    /** @test */
    public function it_has_one_template()
    {
        $contact = Contact::factory()->create([
            'template_id' => Template::factory()->create(),
        ]);

        $this->assertTrue($contact->template()->exists());
    }

    /** @test */
    public function it_has_many_relationships(): void
    {
        $contact = Contact::factory()->create([]);
        $monica = Contact::factory()->create();
        $relationshipType = RelationshipType::factory()->create();

        $contact->relationships()->sync([$monica->id => ['relationship_type_id' => $relationshipType->id]]);

        $this->assertTrue($contact->relationships()->exists());
    }

    /** @test */
    public function it_has_many_labels(): void
    {
        $contact = Contact::factory()->create([]);
        $label = Label::factory()->create();

        $contact->labels()->sync([$label->id]);

        $this->assertTrue($contact->labels()->exists());
    }

    /** @test */
    public function it_has_many_logs(): void
    {
        $contact = Contact::factory()->create();
        ContactLog::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->contactLogs()->exists());
    }

    /** @test */
    public function it_has_many_contact_information(): void
    {
        $contact = Contact::factory()->create();
        ContactInformation::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->contactInformation()->exists());
    }

    /** @test */
    public function it_has_many_addresses(): void
    {
        $contact = Contact::factory()->create();
        Address::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->addresses()->exists());
    }

    /** @test */
    public function it_has_many_notes(): void
    {
        $contact = Contact::factory()->create();
        Note::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->notes()->exists());
    }

    /** @test */
    public function it_has_many_dates(): void
    {
        $contact = Contact::factory()->create();
        ContactImportantDate::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->dates()->exists());
    }

    /** @test */
    public function it_has_many_reminders(): void
    {
        $contact = Contact::factory()->create();
        ContactReminder::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->reminders()->exists());
    }

    /** @test */
    public function it_has_many_loans_as_loaner(): void
    {
        $contact = Contact::factory()->create([]);
        $monica = Contact::factory()->create();
        $loan = Loan::factory()->create();

        $contact->loansAsLoaner()->sync([$loan->id => ['loanee_id' => $monica->id]]);

        $this->assertTrue($contact->loansAsLoaner()->exists());
    }

    /** @test */
    public function it_has_many_loans_as_loanee(): void
    {
        $contact = Contact::factory()->create([]);
        $monica = Contact::factory()->create();
        $loan = Loan::factory()->create();

        $contact->loansAsLoanee()->sync([$loan->id => ['loaner_id' => $monica->id]]);

        $this->assertTrue($contact->loansAsLoanee()->exists());
    }

    /** @test */
    public function it_has_one_company()
    {
        $contact = Contact::factory()->create([
            'company_id' => Company::factory()->create(),
        ]);

        $this->assertTrue($contact->company()->exists());
    }

    /** @test */
    public function it_has_one_current_avatar(): void
    {
        $contact = Contact::factory()->create();
        $avatar = Avatar::factory()->create([
            'contact_id' => $contact->id,
        ]);
        $contact->avatar_id = $avatar->id;
        $contact->save();

        $this->assertTrue($contact->currentAvatar()->exists());
    }

    /** @test */
    public function it_has_many_avatars(): void
    {
        $contact = Contact::factory()->create();
        Avatar::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->avatars()->exists());
    }

    /** @test */
    public function it_has_many_tasks(): void
    {
        $contact = Contact::factory()->create();
        ContactTask::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->tasks()->exists());
    }

    /** @test */
    public function it_has_many_calls(): void
    {
        $contact = Contact::factory()->create();
        Call::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->calls()->exists());
    }

    /** @test */
    public function it_has_many_pets(): void
    {
        $contact = Contact::factory()->create();
        Pet::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->pets()->exists());
    }

    /** @test */
    public function it_has_many_goals(): void
    {
        $contact = Contact::factory()->create();
        Goal::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->goals()->exists());
    }

    /** @test */
    public function it_has_many_files(): void
    {
        $contact = Contact::factory()->create();
        File::factory()->count(2)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertTrue($contact->files()->exists());
    }

    /** @test */
    public function it_has_many_groups(): void
    {
        $contact = Contact::factory()->create();
        $group = Group::factory()->create();
        $contact->groups()->sync([$group->id]);

        $this->assertTrue($contact->groups()->exists());
    }

    /** @test */
    public function it_gets_the_name(): void
    {
        $user = User::factory()->create([
            'name_order' => '%first_name%',
        ]);
        $this->be($user);
        $contact = Contact::factory()->create([
            'first_name' => 'James',
            'last_name' => 'Bond',
            'nickname' => '007',
            'middle_name' => 'W.',
            'maiden_name' => 'Muller',
        ]);

        $this->assertEquals(
            'James',
            $contact->name
        );

        $user->update(['name_order' => '%last_name%']);
        $this->assertEquals(
            'Bond',
            $contact->name
        );

        $user->update(['name_order' => '%first_name% %last_name%']);
        $this->assertEquals(
            'James Bond',
            $contact->name
        );

        $user->update(['name_order' => '%first_name% (%maiden_name%) %last_name%']);
        $this->assertEquals(
            'James (Muller) Bond',
            $contact->name
        );

        $user->update(['name_order' => '%last_name% (%maiden_name%)  || (%nickname%) || %first_name%']);
        $this->assertEquals(
            'Bond (Muller)  || (007) || James',
            $contact->name
        );
    }

    /** @test */
    public function it_gets_the_age(): void
    {
        Carbon::setTestNow(Carbon::create(2022, 1, 1));
        $contact = Contact::factory()->create();
        $type = ContactImportantDateType::factory()->create([
            'vault_id' => $contact->vault_id,
            'internal_type' => ContactImportantDate::TYPE_BIRTHDATE,
        ]);

        $this->assertNull(
            $contact->age
        );

        ContactImportantDate::factory()->create([
            'contact_id' => $contact->id,
            'contact_important_date_type_id' => $type->id,
            'day' => 1,
            'month' => 10,
            'year' => 2000,
        ]);

        $this->assertEquals(
            21,
            $contact->age
        );
    }
}
