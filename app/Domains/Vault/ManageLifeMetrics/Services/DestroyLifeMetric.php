<?php

namespace App\Domains\Vault\ManageLifeMetrics\Services;

use App\Interfaces\ServiceInterface;
use App\Services\BaseService;

class DestroyLifeMetric extends BaseService implements ServiceInterface
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
            'life_metric_id' => 'required|integer|exists:life_metrics,id',
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
     * Destroy a life metric.
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $lifeMetric = $this->vault->lifeMetrics()
            ->findOrFail($data['life_metric_id']);

        $lifeMetric->delete();
    }
}
