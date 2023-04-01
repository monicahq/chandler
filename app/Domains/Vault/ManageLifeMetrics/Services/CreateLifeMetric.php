<?php

namespace App\Domains\Vault\ManageLifeMetrics\Services;

use App\Interfaces\ServiceInterface;
use App\Models\LifeMetric;
use App\Services\BaseService;

class CreateLifeMetric extends BaseService implements ServiceInterface
{
    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'label' => 'required|string|max:255',
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
     * Create a life metric.
     */
    public function execute(array $data): LifeMetric
    {
        $this->validateRules($data);

        $lifeMetric = LifeMetric::create([
            'vault_id' => $data['vault_id'],
            'label' => $data['label'],
        ]);

        return $lifeMetric;
    }
}
