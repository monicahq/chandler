<?php

namespace Tests\Unit\Domains\Vault\ManageKitchen\Web\ViewHelpers;

use App\Domains\Vault\ManageKitchen\Web\ViewHelpers\KitchenIngredientsViewHelper;
use App\Models\Ingredient;
use App\Models\User;
use App\Models\Vault;
use Carbon\Carbon;
use function env;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KitchenIngredientsViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        Carbon::setTestNow(Carbon::create(2022, 1, 1));
        $user = User::factory()->create();
        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
        ]);
        $ingredient = Ingredient::factory()->create([
            'vault_id' => $vault->id,
            'label' => 'label',
        ]);

        $array = KitchenIngredientsViewHelper::data($vault);

        $this->assertArrayHasKey('ingredients', $array);
        $this->assertEquals(
            [
                'store' => env('APP_URL').'/vaults/'.$vault->id.'/kitchen/ingredients',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_dto(): void
    {
        Carbon::setTestNow(Carbon::create(2022, 1, 1));
        $user = User::factory()->create();
        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
        ]);
        $ingredient = Ingredient::factory()->create([
            'vault_id' => $vault->id,
            'label' => 'label',
        ]);

        $array = KitchenIngredientsViewHelper::dto($ingredient);

        $this->assertEquals(
            [
                'id' => $ingredient->id,
                'label' => 'label',
                'url' => [
                    'update' => env('APP_URL').'/vaults/'.$vault->id.'/kitchen/ingredients/'.$ingredient->id,
                    'destroy' => env('APP_URL').'/vaults/'.$vault->id.'/kitchen/ingredients/'.$ingredient->id,
                ],
            ],
            $array
        );
    }
}
