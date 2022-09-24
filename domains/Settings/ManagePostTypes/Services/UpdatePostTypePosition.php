<?php

namespace App\Settings\ManagePostTypes\Services;

use App\Interfaces\ServiceInterface;
use App\Models\PostType;
use App\Models\PostTypeSection;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdatePostTypePosition extends BaseService implements ServiceInterface
{
    private PostType $postType;

    private PostTypeSection $postTypeSection;

    private array $data;

    private int $pastPosition;

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
     * Update the post type position.
     *
     * @param  array  $data
     * @return PostType
     */
    public function execute(array $data): PostType
    {
        $this->data = $data;
        $this->validate();
        $this->updatePosition();

        return $this->postType;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->postType = PostType::where('account_id', $this->data['account_id'])
            ->findOrFail($this->data['post_type_id']);

        $this->pastPosition = $this->postType->position;
    }

    private function updatePosition(): void
    {
        if ($this->data['new_position'] > $this->pastPosition) {
            $this->updateAscendingPosition();
        } else {
            $this->updateDescendingPosition();
        }

        DB::table('post_types')
            ->where('post_type_id', $this->postType->id)
            ->where('id', $this->postType->id)
            ->update([
                'position' => $this->data['new_position'],
            ]);
    }

    private function updateAscendingPosition(): void
    {
        DB::table('post_types')
            ->where('post_type_id', $this->postType->id)
            ->where('position', '>', $this->pastPosition)
            ->where('position', '<=', $this->data['new_position'])
            ->decrement('position');
    }

    private function updateDescendingPosition(): void
    {
        DB::table('post_types')
            ->where('post_type_id', $this->postType->id)
            ->where('position', '>=', $this->data['new_position'])
            ->where('position', '<', $this->pastPosition)
            ->increment('position');
    }
}
