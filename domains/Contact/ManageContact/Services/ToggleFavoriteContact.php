<?php

namespace App\Contact\ManageContact\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\ContactFeedItem;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ToggleFavoriteContact extends BaseService implements ServiceInterface
{
    private array $data;
    private bool $isFavorite;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'author_id' => 'required|integer|exists:users,id',
            'contact_id' => 'required|integer|exists:contacts,id',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     *
     * @return array
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'vault_must_belong_to_account',
            'contact_must_belong_to_vault',
            'author_must_be_vault_editor',
        ];
    }

    /**
     * Toggle the favorite state of a contact for the given user.
     *
     * @param  array  $data
     * @return Contact
     */
    public function execute(array $data): Contact
    {
        $this->data = $data;
        $this->validate();

        $this->toggle();
        $this->updateLastEditedDate();
        $this->createFeedItem();

        return $this->contact;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);
    }

    private function toggle(): void
    {
        $exists = DB::table('contact_vault_user')
            ->where('contact_id', $this->data['contact_id'])
            ->where('vault_id', $this->data['vault_id'])
            ->where('user_id', $this->data['author_id'])
            ->first();

        if ($exists) {
            $this->isFavorite = $exists->is_favorite;

            DB::table('contact_vault_user')
                ->where('contact_id', $this->data['contact_id'])
                ->where('vault_id', $this->data['vault_id'])
                ->where('user_id', $this->data['author_id'])
                ->update(['is_favorite' => ! $this->isFavorite]);
        } else {
            $this->isFavorite = true;

            DB::table('contact_vault_user')->insert([
                'contact_id' => $this->data['contact_id'],
                'vault_id' => $this->data['vault_id'],
                'user_id' => $this->data['author_id'],
                'is_favorite' => true,
                'number_of_views' => 1,
            ]);
        }
    }

    private function updateLastEditedDate(): void
    {
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }

    private function createFeedItem(): void
    {
        ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $this->contact->id,
            'action' => $this->isFavorite ? ContactFeedItem::ACTION_UNFAVORITED_CONTACT : ContactFeedItem::ACTION_FAVORITED_CONTACT,
        ]);
    }
}
