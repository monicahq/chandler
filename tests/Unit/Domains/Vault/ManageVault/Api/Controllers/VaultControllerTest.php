<?php

namespace Tests\Unit\Domains\Vault\ManageVault\Api\Controllers;

use App\Models\Vault;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\ApiTestCase;

class VaultControllerTest extends ApiTestCase
{
    /** @test */
    public function it_gets_a_list_of_vaults(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = $this->createUser();
        Sanctum::actingAs($user, ['read']);

        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
            'name' => 'This is a vault',
            'description' => 'this is a description',
        ]);

        $response = $this->get('/api/vaults');

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                0 => [
                    'id' => $vault->id,
                    'name' => 'This is a vault',
                    'description' => 'this is a description',
                    'created_at' => '2018-01-01T00:00:00Z',
                    'updated_at' => '2018-01-01T00:00:00Z',
                ],
            ],
            'links' => $this->links('/api/vaults'),
            'meta' => $this->meta('/api/vaults'),
        ]);
    }

    /** @test */
    public function it_stores_a_vault(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = $this->createUser();
        Sanctum::actingAs($user, ['read']);

        $form = [
            'name' => 'this is a name',
            'description' => 'this is a description',
        ];
        $response = $this->post('/api/vaults', $form);
        $response->assertStatus(201);

        $vault = Vault::latest()->first();
        $response->assertExactJson([
            'data' => [
                'id' => $vault->id,
                'name' => 'this is a name',
                'description' => 'this is a description',
                'created_at' => '2018-01-01T00:00:00Z',
                'updated_at' => '2018-01-01T00:00:00Z',
            ],
        ]);
    }

    /** @test */
    public function it_gets_a_vault(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = $this->createUser();
        Sanctum::actingAs($user, ['read']);

        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
            'name' => 'This is a vault',
            'description' => 'this is a description',
        ]);

        $response = $this->get('/api/vaults/'.$vault->id);

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'id' => $vault->id,
                'name' => 'This is a vault',
                'description' => 'this is a description',
                'created_at' => '2018-01-01T00:00:00Z',
                'updated_at' => '2018-01-01T00:00:00Z',
            ],
        ]);
    }

    /** @test */
    public function it_updates_a_vault(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = $this->createUser();
        Sanctum::actingAs($user, ['read']);
        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
            'name' => 'This is a vault',
            'description' => 'this is a description',
        ]);
        $vault = $this->setPermissionInVault($user, Vault::PERMISSION_MANAGE, $vault);

        $form = [
            'account_id' => $user->account_id,
            'author_id' => $user->id,
            'vault_id' => $vault->id,
            'name' => 'this is a name',
            'description' => 'this is a cool description',
        ];

        $response = $this->put('/api/vaults/'.$vault->id, $form);
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                'id' => $vault->id,
                'name' => 'this is a name',
                'description' => 'this is a cool description',
                'created_at' => '2018-01-01T00:00:00Z',
                'updated_at' => '2018-01-01T00:00:00Z',
            ],
        ]);
    }

    /** @test */
    public function it_destroys_a_vault(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = $this->createUser();
        Sanctum::actingAs($user, ['read']);
        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
        ]);
        $vault = $this->setPermissionInVault($user, Vault::PERMISSION_MANAGE, $vault);

        $form = [
            'account_id' => $user->account_id,
            'author_id' => $user->id,
            'vault_id' => $vault->id,
        ];

        $response = $this->delete('/api/vaults/'.$vault->id, $form);
        $response->assertStatus(200);

        $response->assertExactJson([
            'deleted' => true,
            'id' => $vault->id,
        ]);
    }
}
