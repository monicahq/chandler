<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTemplateSection extends Model
{
    use HasFactory;

    protected $table = 'post_template_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'post_template_id',
        'label',
        'position',
    ];

    /**
     * Get the post type associated with the post type section.
     *
     * @return BelongsTo
     */
    public function postTemplate(): BelongsTo
    {
        return $this->belongsTo(PostTemplate::class);
    }
}
