<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'rank',
        'branch',
        'unit',
        'sponsor_name',
        'sponsor_phone',
        'pcs_date',
        'deros_date',
        'family_size',
        'has_pets',
        'pet_details',
        'oha_amount',
        'oha_currency',
        'special_requirements',
    ];

    protected $casts = [
        'pcs_date' => 'date',
        'deros_date' => 'date',
        'family_size' => 'integer',
        'has_pets' => 'boolean',
        'oha_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
