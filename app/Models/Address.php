<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'vault_id',
        'address_type_id',
        'line_1',
        'line_2',
        'city',
        'province',
        'postal_code',
        'country',
        'latitude',
        'longitude',
    ];

    /**
     * Get the contacts associated with the address.
     *
     * @return BelongsToMany
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_address');
    }

    /**
     * Get the address type object associated with the address.
     *
     * @return BelongsTo
     */
    public function addressType(): BelongsTo
    {
        return $this->belongsTo(AddressType::class);
    }

    /**
     * Get the address's feed item.
     *
     * @return MorphOne
     */
    public function feedItem(): MorphOne
    {
        return $this->morphOne(ContactFeedItem::class, 'feedable');
    }
}
