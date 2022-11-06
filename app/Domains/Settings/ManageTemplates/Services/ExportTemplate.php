<?php

namespace App\Domains\Settings\ManageTemplates\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Template;
use App\Services\BaseService;
use Symfony\Component\Yaml\Yaml;

class ExportTemplate extends BaseService implements ServiceInterface
{
    private Template $template;

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
            'template_id' => 'required|integer|exists:templates,id',
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
     * Export a template to a yml file.
     *
     * @param  array  $data
     * @retur
     */
    public function execute(array $data)
    {
        $this->validateRules($data);

        $this->template = Template::where('account_id', $data['account_id'])
            ->findOrFail($data['template_id']);

        $pages = $this->template->pages()
            ->orderBy('position')
            ->with('modules')
            ->get();

        $pagesArray = [];
        foreach ($pages as $page) {
            $modules = $page->modules()->orderBy('position')->get();
            $modulesArray = [];
            foreach ($modules as $module) {
                array_push($modulesArray, [
                    'position' => $module->pivot->position,
                    'name' => $module->name,
                    'type' => $module->type,
                ]);
            }

            array_push($pagesArray, [
                'name' => $page->name,
                'slug' => $page->slug,
                'position' => $page->position,
                'type' => $page->type,
                'modules' => $modulesArray,
            ]);
        }

        $data = [
            'name' => $this->template->name,
            'pages' => $pagesArray,
        ];

        $yml = Yaml::dump($data, 2);
        dd($yml);
    }
}
