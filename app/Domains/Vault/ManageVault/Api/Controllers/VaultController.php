<?php

namespace App\Domains\Vault\ManageVault\Api\Controllers;

use App\Domains\Vault\ManageVault\Services\CreateVault;
use App\Domains\Vault\ManageVault\Services\DestroyVault;
use App\Domains\Vault\ManageVault\Services\UpdateVault;
use App\Http\Controllers\ApiController;
use App\Http\Resources\VaultResource;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaultController extends ApiController
{
    /**
     * List all vaults
     *
     * Get all the vaults in the account.
     *
     * @group Vault management
     * @subgroup Vaults
     * @queryParam limit int A limit on the number of objects to be returned. Limit can range between 1 and 100, and the default is 10. Example: 10
     * @apiResourceModel \App\Models\Vault
     */
    public function index(Request $request)
    {
        try {
            $vaults = Auth::user()->account->vaults()
                ->paginate($this->getLimitPerPage());
        } catch (QueryException) {
            return $this->respondInvalidQuery();
        }

        return VaultResource::collection($vaults);
    }

    /**
     * Create a vault
     *
     * Creates a vault object.
     *
     * @group Vault management
     * @subgroup Vaults
     * @apiResourceModel \App\Models\Vault
     * @bodyParam name string required The name of the vault. Max 255 characters.
     * @bodyParam description string The description of the vault. Max 65535 characters.
     */
    public function store(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'type' => Vault::TYPE_PERSONAL,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];

        $vault = (new CreateVault())->execute($data);

        return new VaultResource($vault);
    }

    /**
     * Retrieve a vault
     *
     * Get a specific vault object.
     *
     * @group Vault management
     * @subgroup Vaults
     * @apiResourceModel \App\Models\Vault
     */
    public function show(Request $request, int $vaultId)
    {
        try {
            $vault = Vault::where('account_id', Auth::user()->account_id)
                ->findOrFail($vaultId);
        } catch (ModelNotFoundException) {
            return $this->respondNotFound();
        }

        return new VaultResource($vault);
    }

    /**
     * Update a vault
     *
     * Updates a vault object.
     *
     * @group Vault management
     * @subgroup Vaults
     * @apiResourceModel \App\Models\Vault
     * @urlParam id int required The vault's ID.
     * @bodyParam name string required The name of the vault. Max 255 characters.
     * @bodyParam description string The description of the vault. Max 65535 characters.
     */
    public function update(Request $request, int $vaultId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];

        $vault = (new UpdateVault())->execute($data);

        return new VaultResource($vault);
    }

    /**
     * Delete a vault
     *
     * Destroys a vault object.
     * Warning: everything in the vault will be immediately deleted.
     *
     * @group Vault management
     * @subgroup Vaults
     * @apiResourceModel \App\Models\Vault
     * @urlParam id int required The vault's ID.
     */
    public function destroy(Request $request, int $vaultId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
        ];

        (new DestroyVault())->execute($data);

        return $this->respondObjectDeleted($vaultId);
    }
}
