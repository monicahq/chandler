<?php

namespace App\Domains\Vault\ManageVaultSettings\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Label;
use App\Services\BaseService;
use Illuminate\Support\Str;

class CreateLabel extends BaseService implements ServiceInterface
{
    private Label $label;

    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|uuid|exists:accounts,id',
            'author_id' => 'required|uuid|exists:users,id',
            'vault_id' => 'required|uuid|exists:vaults,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'bg_color' => 'string|max:255',
            'text_color' => 'string|max:255',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'author_must_be_vault_editor',
            'vault_must_belong_to_account',
        ];
    }

    /**
     * Create a label.
     */
    public function execute(array $data): Label
    {
        $this->validateRules($data);

        $this->label = Label::create([
            'vault_id' => $data['vault_id'],
            'name' => $data['name'],
            'description' => $this->valueOrNull($data, 'description'),
            'slug' => Str::slug($data['name'], '-'),
            'bg_color' => $data['bg_color'],
            'text_color' => $data['text_color'],
        ]);

        return $this->label;
    }
}
