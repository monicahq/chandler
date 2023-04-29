<?php

namespace Tests\Unit\Domains\Settings\ManageSubscription\Services;

use App\Domains\Settings\ManageSubscription\Services\ActivateLicenceKey;
use App\Domains\Settings\ManageSubscription\Services\CallCustomerPortal;
use App\Exceptions\LicenceKeyDontExistException;
use App\Exceptions\LicenceKeyErrorException;
use App\Exceptions\LicenceKeyInvalidException;
use App\Exceptions\MissingPrivateKeyException;
use App\Models\Account;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Tests\TestCase;

class ActivateLicenceKeyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_activates_a_licence_key()
    {
        config([
            'monica.customer_portal_private_key' => 'base64:CiZYhXuxFaXsYWOTw8o6C82rqiZkphLg+N6fVep2l0M=',
            'monica.customer_portal_url' => 'http://customers.test',
            'monica.customer_portal_client_id' => '1',
            'monica.customer_portal_client_secret' => '1',
        ]);

        $key = 'eyJpdiI6IjJ5clV4N3hlaThIMGtsckgiLCJ2YWx1ZSI6IkdIUWd4aFM4OWlHL2V3SHF0M1VOazVYTjBaN2c4RGpRUDZtN0VNejhGL0YzZGF6bmFBNnBzK3lUT0VVVXFIaE80SlZ3RmRRK3J4UTRBQU5XU2lUS3JhRHQ0d1paYUIrNGM0VUg2ZzBNU3Y4MjlzQ0d4N2pTZGlPY3E5UWFMRGJCSXdZSnN6a1MwYVg5RFBaQ01jMGtpMzhubnVFbmV5TXB3Zz09IiwibWFjIjoiIiwidGFnIjoiWVZlUjgrQU8yUlBCd1BaTDUxb1JJZz09In0=';
        $account = Account::factory()->create([]);

        $request = [
            'account_id' => $account->id,
            'licence_key' => $key,
        ];

        $this->mock(App\Domains\Settings\ManageSubscription\Services\CallCustomerPortal::class, function (MockInterface $mock) use ($key) {
            $mock->shouldReceive('execute')
                ->once()
                ->with(['licence_key' => $key])
                ->andReturn(200);
        });

        (new ActivateLicenceKey())->execute($request);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'licence_key' => $key,
            'valid_until_at' => '2022-04-03',
            'purchaser_email' => 'admin@admin.com',
            'frequency' => 'monthly',
        ]);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given()
    {
        $request = [];

        $this->expectException(ValidationException::class);
        (new ActivateLicenceKey())->execute($request);
    }

    /** @test */
    public function it_fails_if_the_licence_key_is_empty()
    {
        $this->expectException(ValidationException::class);

        $account = Account::factory()->create([]);

        $request = [
            'account_id' => $account->id,
            'licence_key' => '',
        ];

        (new ActivateLicenceKey())->execute($request);
    }

    /** @test */
    public function it_fails_if_private_key_is_not_set()
    {
        config(['monica.customer_portal_private_key' => null]);

        $this->expectException(MissingPrivateKeyException::class);

        $account = Account::factory()->create([]);

        $request = [
            'account_id' => $account->id,
            'licence_key' => 'test',
        ];

        (new ActivateLicenceKey())->execute($request);
    }

    /** @test */
    public function it_fails_if_the_licence_key_does_not_exist()
    {
        config(['monica.customer_portal_private_key' => 'x']);

        $this->expectException(LicenceKeyDontExistException::class);

        $key = 'x';

        $this->mock(CallCustomerPortal::class, function (MockInterface $mock) use ($key) {
            $mock->shouldReceive('execute')
                ->once()
                ->with(['licence_key' => $key])
                ->andReturn(404);
        });

        $account = Account::factory()->create([]);

        $request = [
            'account_id' => $account->id,
            'licence_key' => $key,
        ];

        (new ActivateLicenceKey())->execute($request);
    }

    /** @test */
    public function it_fails_if_the_licence_key_is_not_valid_anymore()
    {
        config(['monica.customer_portal_private_key' => 'x']);

        $this->expectException(LicenceKeyInvalidException::class);

        $key = 'x';

        $this->mock(CallCustomerPortal::class, function (MockInterface $mock) use ($key) {
            $mock->shouldReceive('execute')
                ->once()
                ->with(['licence_key' => $key])
                ->andReturn(410);
        });

        $account = Account::factory()->create([]);

        $request = [
            'account_id' => $account->id,
            'licence_key' => $key,
        ];

        (new ActivateLicenceKey())->execute($request);
    }

    /** @test */
    public function it_fails_if_there_is_an_error_during_validation()
    {
        config(['monica.customer_portal_private_key' => 'x']);

        $this->expectException(LicenceKeyErrorException::class);

        $key = 'x';

        $this->mock(CallCustomerPortal::class, function (MockInterface $mock) use ($key) {
            $mock->shouldReceive('execute')
                ->once()
                ->with(['licence_key' => $key])
                ->andReturn(500);
        });

        $account = Account::factory()->create([]);

        $request = [
            'account_id' => $account->id,
            'licence_key' => $key,
        ];

        (new ActivateLicenceKey())->execute($request);
    }
}
