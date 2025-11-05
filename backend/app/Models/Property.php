<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Property extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'landlord_id',
        // Address
        'street_name',
        'house_number',
        'apt_number',
        'city',
        'province',
        'postal_code',
        'country',
        // Cadastral Data
        'cadastral_sheet_number',
        'cadastral_plot_number',
        'cadastral_unit_number',
        'cadastral_tax_evaluation',
        'cadastral_category',
        // Rooms
        'living_rooms',
        'dining_rooms',
        'bedrooms',
        'bathrooms',
        'kitchen',
        'basement',
        'attic',
        'garage',
        'yard',
        // Property Details
        'furnished',
        'pets_allowed',
        'heating_type',
        'cooling_type',
        // Status
        'status',
        'ho_reviewed_at',
        'ho_reviewer_id',
        'ho_comments',
    ];

    protected $casts = [
        'furnished' => 'boolean',
        'pets_allowed' => 'boolean',
        'basement' => 'boolean',
        'attic' => 'boolean',
        'garage' => 'boolean',
        'yard' => 'boolean',
        'ho_reviewed_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const HEATING_CITY_GAS = 'city_gas';
    const HEATING_LPG_COUPONS = 'lpg_coupons';
    const HEATING_LPG_NO_COUPONS = 'lpg_no_coupons';
    const HEATING_FUEL = 'fuel';
    const HEATING_ELECTRIC = 'electric';
    const HEATING_SEPARATE_SYSTEM = 'separate_system';
    const HEATING_SEPARATE_METER = 'separate_meter';
    const HEATING_SHARED_US = 'shared_us';
    const HEATING_SHARED_ITALIANS = 'shared_italians';

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function listing(): HasOne
    {
        return $this->hasOne(Listing::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function hoReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ho_reviewer_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_name,
            $this->house_number,
            $this->apt_number ? "Apt {$this->apt_number}" : null,
            $this->city,
            $this->province,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}