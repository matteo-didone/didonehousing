<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'tax_id',
        'business_type',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'bank_name',
        'iban',
        'swift_bic',
        'cedolare_secca',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'cedolare_secca' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
