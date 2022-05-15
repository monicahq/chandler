<?php

namespace App\Settings\ManageTemplates\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Template;
use App\Models\TemplatePage;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdateTemplatePagePosition extends BaseService implements ServiceInterface
{
    private Template $template;
    private TemplatePage $templatePage;

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
            'template_page_id' => 'required|integer|exists:template_pages,id',
            'new_position' => 'required|integer',
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
     * Update the template page position.
     *
     * @param  array  $data
     * @return TemplatePage
     */
    public function execute(array $data): TemplatePage
    {
        $this->data = $data;
        $this->validate();
        $this->updatePosition();

        return $this->templatePage;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->template = Template::where('account_id', $this->data['account_id'])
            ->findOrFail($this->data['template_id']);

        $this->templatePage = TemplatePage::where('template_id', $this->data['template_id'])
            ->findOrFail($this->data['template_page_id']);

        $this->pastPosition = $this->templatePage->position;
    }

    private function updatePosition(): void
    {
        if ($this->data['new_position'] > $this->pastPosition) {
            $this->updateAscendingPosition();
        } else {
            $this->updateDescendingPosition();
        }

        DB::table('template_pages')
            ->where('template_id', $this->template->id)
            ->where('id', $this->templatePage->id)
            ->update([
                'position' => $this->data['new_position'],
            ]);
    }

    private function updateAscendingPosition(): void
    {
        DB::table('template_pages')
            ->where('template_id', $this->template->id)
            ->where('position', '>', $this->pastPosition)
            ->where('position', '<=', $this->data['new_position'])
            ->decrement('position');
    }

    private function updateDescendingPosition(): void
    {
        DB::table('template_pages')
            ->where('template_id', $this->template->id)
            ->where('position', '>=', $this->data['new_position'])
            ->where('position', '<', $this->pastPosition)
            ->increment('position');
    }
}
