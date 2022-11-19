<?php

namespace Tests\Unit\Domains\Settings\ManageUsers\Api\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_current_user(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = $this->createUser();
        Sanctum::actingAs($user, ['read']);

        $response = $this->get('/api/user');

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->first_name.' '.$user->last_name,
                'email' => $user->email,
                'created_at' => '2018-01-01T00:00:00Z',
                'updated_at' => '2018-01-01T00:00:00Z',
            ],
        ]);
    }
}
