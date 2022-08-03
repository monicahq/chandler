<?php

namespace App\Contact\ManageAvatar\Services;

use App\Helpers\AvatarHelper;
use App\Interfaces\ServiceInterface;
use App\Models\Avatar;
use App\Models\File;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;

class DestroyAvatar extends BaseService implements ServiceInterface
{
    private Avatar $avatar;

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
            'author_must_be_vault_editor',
            'contact_must_belong_to_vault',
        ];
    }

    /**
     * Remove the current file used as avatar and put the default avatar back.
     *
     * @param  array  $data
     * @return Avatar
     */
    public function execute(array $data): Avatar
    {
        $this->data = $data;
        $this->validate();

        $this->getCurrentAvatar();
        $this->setNewAvatar();
        $this->updateLastEditedDate();

        return $this->avatar;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);
    }

    private function getCurrentAvatar(): void
    {
        $this->avatar = $this->contact->currentAvatar;

        if ($this->avatar->type !== Avatar::TYPE_FILE) {
            throw new Exception('The contact does not have a photo as avatar.');
        }

        $this->avatar->delete();
    }

    private function setNewAvatar(): void
    {
        $this->avatar = AvatarHelper::generateRandomAvatar($this->contact);
        $this->contact->avatar_id = $this->avatar->id;
        $this->contact->save();
    }

    private function updateLastEditedDate(): void
    {
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }
}
