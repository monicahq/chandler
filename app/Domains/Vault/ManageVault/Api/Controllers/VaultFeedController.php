<?php

namespace App\Domains\Vault\ManageVault\Api\Controllers;

use App\Domains\Contact\ManageContactFeed\Web\ViewHelpers\ModuleFeedViewHelper;
use App\Http\Controllers\ApiController;
use App\Http\Resources\VaultFeedResource;
use App\Models\Contact;
use App\Models\ContactFeedItem;
use App\Models\Vault;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

/**
 * @group Vault management
 * @subgroup Vault feed
 */
class VaultFeedController extends ApiController
{
    public function __construct()
    {
        $this->middleware('abilities:read')->only(['index']);

        parent::__construct();
    }

    #[Endpoint('List the feed of the vault', description: 'Get all the activity
        that happened in the vault. As this method gets the activity in a vault,
        there might be a user that did some actions but was deleted. In this case,
        the user will be replaced by null.')]
    #[QueryParam('limit', 'int', description: 'A limit on the number of objects to be returned. Limit can range between 1 and 100, and the default is 10.', required: false, example: 10)]
    #[ResponseFromApiResource(VaultFeedResource::class, Vault::class, collection: true)]
    public function index(Request $request, int $vaultId)
    {
        $vault = $request->user()->account->vaults()
            ->findOrFail($vaultId);

        $contactIds = Contact::where('vault_id', $vault->id)->select('id')->get()->toArray();

        $items = ContactFeedItem::whereIn('contact_id', $contactIds)
            ->with([
                'author',
                'contact' => [
                    'importantDates',
                ],
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($this->getLimitPerPage());

        return ModuleFeedViewHelper::data($items, $request->user(), $vault);
    }
}
