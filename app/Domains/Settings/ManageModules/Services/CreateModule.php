<?php

namespace App\Domains\Settings\ManageModules\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Module;
use App\Models\ModuleRow;
use App\Models\User;
use App\Services\BaseService;

class CreateModule extends BaseService implements ServiceInterface
{
    private array $data;

    private Module $module;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'rows' => 'required|array',
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
            'author_must_be_account_administrator',
        ];
    }

    /**
     * Create a module.
     *
     * @param  array  $data
     * @return Module
     */
    public function execute(array $data): Module
    {
        $this->data = $data;
        $this->validateRules($data);

        $this->createModule();
        $this->analyzeRows();

        return $this->module;
    }

    private function createModule(): void
    {
        $this->module = Module::create([
            'account_id' => $this->data['account_id'],
            'name' => $this->data['name'],
            'type' => Module::TYPE_CUSTOM,
            'reserved_to_contact_information' => false,
            'can_be_deleted' => true,
        ]);
    }

    private function analyzeRows(): void
    {
        $rows = $this->data['rows'];

        foreach ($rows as $row) {
            $this->analyzeRow($row['fields']);
        }
    }

    private function analyzeRow(array $row): void
    {
        $row = ModuleRow::create([
            'module_id' => $this->module->id,
            'position' => $row['position'],
        ]);

        $this->analyzeFields($row['fields'], $row);
    }

    private function analyzeFields(array $fields, ModuleRow $row): void
    {
        foreach ($fields as $field) {
            $this->analyzeField($field, $row);
        }
    }

    private function analyzeField(array $field, ModuleRow $row): void
    {
        if ($field['type'] == 'input') {
            $this->addInputField($field);
        }
    }
}
