<?php

namespace App\Settings\ManagePostTypes\Services;

use App\Interfaces\ServiceInterface;
use App\Models\PostType;
use App\Services\BaseService;

class DestroyPostType extends BaseService implements ServiceInterface
{
    private PostType $postType;

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
     * Destroy a post type.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->postType = PostType::where('account_id', $data['account_id'])
            ->findOrFail($data['post_type_id']);

        $this->postType->delete();
    }
}
