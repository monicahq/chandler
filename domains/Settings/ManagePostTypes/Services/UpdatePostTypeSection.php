<?php

namespace App\Settings\ManagePostTypes\Services;

use App\Interfaces\ServiceInterface;
use App\Models\PostType;
use App\Models\PostTypeSection;
use App\Services\BaseService;

class UpdatePostTypeSection extends BaseService implements ServiceInterface
{
    private PostTypeSection $postTypeSection;

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
            'post_type_id' => 'required|integer|exists:post_types,id',
            'post_type_section_id' => 'required|integer|exists:post_type_sections,id',
            'label' => 'required|string|max:255',
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
     * Update a post type section.
     *
     * @param  array  $data
     * @return PostTypeSection
     */
    public function execute(array $data): PostTypeSection
    {
        $this->validateRules($data);

        PostType::where('account_id', $data['account_id'])
            ->findOrFail($data['post_type_id']);

        $this->postTypeSection = PostTypeSection::where('post_type_id', $data['post_type_id'])
            ->findOrFail($data['post_type_section_id']);

        $this->postTypeSection->label = $data['label'];
        $this->postTypeSection->save();

        return $this->postTypeSection;
    }
}
