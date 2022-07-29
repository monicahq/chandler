<?php

namespace App\Contact\ManageAvatar\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Call;
use App\Models\File;
use App\Services\BaseService;
use Carbon\Carbon;

class UpdatePhotoAsAvatar extends BaseService implements ServiceInterface
{
    private File $file;

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
            'file_id' => 'nullable|integer|exists:file_id,id',
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
     * Set the given photo as the contact's avatar.
     *
     * @param  array  $data
     * @return Call
     */
    public function execute(array $data): Call
    {
        $this->data = $data;
        $this->validate();

        $this->createCall();
        $this->updateLastEditedDate();

        return $this->file;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->file = File::where('contact_id', $this->data['contact_id'])
            ->where('type', File::TYPE_PHOTO)
            ->findOrFail($this->data['file_id']);
    }

    private function createCall(): void
    {
        $this->call = Call::create([
            'contact_id' => $this->data['contact_id'],
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'called_at' => $this->data['called_at'],
            'duration' => $this->valueOrNull($this->data, 'duration'),
            'call_reason_id' => $this->valueOrNull($this->data, 'call_reason_id'),
            'emotion_id' => $this->valueOrNull($this->data, 'emotion_id'),
            'description' => $this->valueOrNull($this->data, 'description'),
            'type' => $this->data['type'],
            'answered' => $this->valueOrTrue($this->data, 'answered'),
            'who_initiated' => $this->data['who_initiated'],
        ]);
    }

    private function updateLastEditedDate(): void
    {
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }
}
