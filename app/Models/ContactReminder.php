<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactReminder extends Model
{
    use HasFactory;

    protected $table = 'contact_reminders';

    /**
     * Possible type.
     */
    const TYPE_ONE_TIME = 'one_time';
    const TYPE_RECURRING_DAY = 'recurring_day';
    const TYPE_RECURRING_MONTH = 'recurring_month';
    const TYPE_RECURRING_YEAR = 'recurring_year';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'name',
        'date_to_be_reminded_of',
        'frequency',
        'frequency_number',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date_to_be_reminded_of',
    ];

    /**
     * Get the contact associated with the contact date.
     *
     * @return BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
