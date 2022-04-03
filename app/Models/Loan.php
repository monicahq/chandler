<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory, Searchable;

    /**
     * Possible types.
     */
    const TYPE_DEBT = 'debt';
    const TYPE_LOAN = 'loan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'type',
        'name',
        'description',
        'amount_lent',
        'loaned_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'loaned_at',
    ];

    /**
     * Get the contact associated with the loan.
     *
     * @return BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the currency associated with the loan.
     *
     * @return BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the contact that did the loan.
     *
     * @return BelongsTo
     */
    public function loaners()
    {
        return $this->belongsToMany(Contact::class, 'contact_loan', 'loan_id', 'loaner_id');
    }

    /**
     * Get the contact records the loan was made to.
     *
     * @return BelongsTo
     */
    public function loanees()
    {
        return $this->belongsToMany(Contact::class, 'contact_loan', 'loan_id', 'loanee_id');
    }

    /**
     * Get the loan's feed item.
     */
    public function feedItem()
    {
        return $this->morphOne(ContactFeedItem::class, 'feedable');
    }
}
