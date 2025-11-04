<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'tax_id',
        'services_offered',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'service_area',
        'website',
        'average_rating',
        'total_jobs',
        'is_verified',
        'verified_at',
        'emergency_available',
    ];

    protected $casts = [
        'services_offered' => 'array',
        'service_area' => 'array',
        'average_rating' => 'decimal:2',
        'total_jobs' => 'integer',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'emergency_available' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
