<?php

namespace Tests\Unit\Domains\Contact\ManageAvatar\Services;

use App\Contact\ManageAvatar\Services\DestroyAvatar;
use App\Models\Account;
use App\Models\Avatar;
use App\Models\Contact;
use App\Models\File;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DestroyAvatarTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_deletes_an_avatar_if_its_a_photo(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault($user->account);
        $vault = $this->setPermissionInVault($user, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $file = File::factory()->create([
            'contact_id' => $contact->id,
            'type' => File::TYPE_AVATAR,
        ]);
        $avatar = Avatar::factory()->create([
            'type' => Avatar::TYPE_FILE,
            'file_id' => $file->id,
        ]);
        $contact->avatar_id = $avatar->id;
        $contact->save();

        $this->executeService($user, $user->account, $vault, $contact);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyAvatar())->execute($request);
    }

    private function executeService(User $author, Account $account, Vault $vault, Contact $contact): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'contact_id' => $contact->id,
        ];

        $avatar = (new DestroyAvatar())->execute($request);

        $this->assertDatabaseHas('avatars', [
            'id' => $avatar->id,
            'file_id' => null,
            'type' => Avatar::TYPE_GENERATED,
        ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'avatar_id' => $avatar->id,
        ]);
    }
}
