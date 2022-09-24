<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTypeSection extends Model
{
    use HasFactory;

    protected $table = 'post_type_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'post_type_id',
        'label',
        'position',
    ];

    /**
     * Get the post type associated with the post type section.
     *
     * @return BelongsTo
     */
    public function postType(): BelongsTo
    {
        return $this->belongsTo(PostType::class);
    }
}
