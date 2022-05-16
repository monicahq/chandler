<?php

namespace Tests\Unit\Domains\Settings\ManageCallReasons\Services;

use App\Exceptions\NotEnoughPermissionException;
use App\Jobs\CreateAuditLog;
use App\Models\Account;
use App\Models\CallReason;
use App\Models\CallReasonType;
use App\Models\RelationshipGroupType;
use App\Models\User;
use App\Settings\ManageCallReasons\Services\CreateCallReasonType;
use App\Settings\ManageRelationshipTypes\Services\CreateRelationshipGroupType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateCallReasonTypeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_call_reason_type(): void
    {
        $ross = $this->createAdministrator();
        $this->executeService($ross, $ross->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateCallReasonType)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $this->executeService($ross, $account);
    }

    /** @test */
    public function it_fails_if_user_is_not_administrator(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $this->executeService($ross, $ross->account);
    }

    private function executeService(User $author, Account $account): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'label' => 'type name',
        ];

        $type = (new CreateCallReasonType)->execute($request);

        $this->assertDatabaseHas('call_reason_types', [
            'id' => $type->id,
            'account_id' => $account->id,
            'label' => 'type name',
        ]);

        $this->assertInstanceOf(
            CallReasonType::class,
            $type
        );
    }
}
