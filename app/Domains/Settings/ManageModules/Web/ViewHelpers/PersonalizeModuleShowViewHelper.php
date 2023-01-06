<?php

namespace App\Domains\Settings\ManageModules\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\Module;
use App\Models\ModuleRow;
use App\Models\ModuleRowField;
use App\Models\ModuleRowFieldChoice;
use App\Models\User;

class PersonalizeModuleShowViewHelper
{
    public static function data(Module $module, User $user): array
    {
        $rowsCollection = $module->rows()
            ->get()
            ->map(fn (ModuleRow $row) => self::dtoModuleRow($row));

        return [
            'id' => $module->id,
            'name' => $module->name,
            'module_rows' => $rowsCollection,
            'created_at' => DateHelper::format($module->created_at, $user),
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'modules' => route('settings.personalize.module.index'),
                'destroy' => route('settings.personalize.module.destroy', [
                    'module' => $module->id,
                ]),
            ],
        ];
    }

    private static function dtoModuleRow(ModuleRow $row): array
    {
        $fieldsCollection = $row
            ->fields()
            ->orderBy('position', 'asc')
            ->get()
            ->map(fn (ModuleRowField $field) => self::dtoModuleRowField($field));

        return [
            'id' => $row->id,
            'module_row_fields' => $fieldsCollection,
        ];
    }

    private static function dtoModuleRowField(ModuleRowField $moduleRowField): array
    {
        $choicesCollection = $moduleRowField
            ->choices()
            ->get()
            ->map(fn (ModuleRowFieldChoice $choice) => self::dtoModuleRowFieldChoice($choice));

        return [
            'id' => $moduleRowField->id,
            'label' => $moduleRowField->label,
            'help' => $moduleRowField->help,
            'placeholder' => $moduleRowField->placeholder,
            'required' => $moduleRowField->required,
            'module_field_type' => $moduleRowField->module_field_type,
            'choices' => $choicesCollection,
        ];
    }

    private static function dtoModuleRowFieldChoice(ModuleRowFieldChoice $choice): array
    {
        return [
            'id' => $choice->id,
            'label' => $choice->label,
        ];
    }
}
