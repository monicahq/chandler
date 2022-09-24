<?php

namespace App\Settings\ManagePostTypes\Services;

use App\Interfaces\ServiceInterface;
use App\Models\PostType;
use App\Models\Template;
use App\Services\BaseService;

class CreatePostType extends BaseService implements ServiceInterface
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
     * Create a post type.
     *
     * @param  array  $data
     * @return PostType
     */
    public function execute(array $data): PostType
    {
        $this->validateRules($data);

        // determine the new position of the template page
        $newPosition = PostType::where('account_id', $data['account_id'])
            ->max('position');
        $newPosition++;

        $this->postType = PostType::create([
            'account_id' => $data['account_id'],
            'label' => $data['label'],
            'position' => $newPosition,
        ]);

        return $this->postType;
    }
}
