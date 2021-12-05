<?php

namespace Tests\Features\Vault\ManageVault\ViewHelpers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vault;
use App\Models\Account;
use App\Models\Contact;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Features\Vault\ManageVault\Services\CreateVault;
use App\Features\Vault\ManageVault\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VaultIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_general_layout_information(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $array = VaultIndexViewHelper::loggedUserInformation();
        $this->assertEquals(
            [
                'name' => $user->name,
            ],
            $array
        );
    }
}
