<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasFactory, HasApiTokens;

    /**
     * Available names order.
     *
     * @var array
     */
    protected const NAMES_ORDER = [
        'firstname_lastname',
        'lastname_firstname',
        'firstname_lastname_nickname',
        'firstname_nickname_lastname',
        'lastname_firstname_nickname',
        'lastname_nickname_firstname',
        'nickname_firstname_lastname',
        'nickname_lastname_firstname',
        'nickname',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'is_account_administrator',
        'invitation_code',
        'invitation_accepted_at',
        'name_order',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'invitation_accepted_at' => 'datetime',
        'is_account_administrator' => 'boolean',
    ];

    /**
     * Get the account record associated with the user.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the vault records associated with the user.
     *
     * @return BelongsToMany
     */
    public function vaults()
    {
        return $this->belongsToMany(Vault::class)->withTimestamps()->withPivot('permission');
    }

    /**
     * Get the note records associated with the user.
     *
     * @return HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'author_id');
    }

    /**
     * Get the name of the user.
     *
     * @param  mixed  $value
     * @return null|string
     */
    public function getNameAttribute($value): ?string
    {
        if (! $this->first_name) {
            return null;
        }

        $name = '';

        switch ($this->name_order) {
            case 'firstname_lastname':
            case 'firstname_nickname_lastname':
            case 'firstname_lastname_nickname':
            case 'nickname_firstname_lastname':
            case 'nickname':
                $name = $this->first_name.' '.$this->last_name;
                break;

            default:
                $name = $this->last_name.' '.$this->first_name;
                break;
        }

        return $name;
    }
}
