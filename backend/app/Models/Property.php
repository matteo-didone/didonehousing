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
        // Google Maps Integration
        'google_place_id',
        'latitude',
        'longitude',
        'distance_from_base_km',
        'formatted_address',
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
        'bathrooms', // Kept for backward compatibility
        'full_bathrooms',
        'half_bathrooms',
        'kitchen',
        'basement',
        'attic',
        'garage',
        'yard',
        // Property Details
        'furnishing_status',
        'pets_allowed',
        'pets_notes',
        'heating_type',
        'heating_system',
        'has_heat_meter',
        'heating_notes',
        'cooling_type',
        // Redecoration
        'redecoration_fees_required',
        'redecoration_fees_amount',
        'redecoration_date',
        // Additional Details
        'floor_number',
        'total_floors',
        'elevator',
        'balcony',
        'terrace',
        'total_sqm',
        'energy_class',
        'year_built',
        // Status
        'status',
        'ho_reviewed_at',
        'ho_reviewer_id',
        'ho_comments',
    ];

    protected $casts = [
        // Booleans
        'pets_allowed' => 'boolean',
        'basement' => 'boolean',
        'attic' => 'boolean',
        'garage' => 'boolean',
        'yard' => 'boolean',
        'has_heat_meter' => 'boolean',
        'redecoration_fees_required' => 'boolean',
        'elevator' => 'boolean',
        'balcony' => 'boolean',
        'terrace' => 'boolean',
        // Decimals
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_from_base_km' => 'decimal:2',
        'redecoration_fees_amount' => 'decimal:2',
        'total_sqm' => 'decimal:2',
        // Dates
        'redecoration_date' => 'date',
        'ho_reviewed_at' => 'datetime',
        // Integers
        'living_rooms' => 'integer',
        'dining_rooms' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'full_bathrooms' => 'integer',
        'half_bathrooms' => 'integer',
        'kitchen' => 'integer',
        'floor_number' => 'integer',
        'total_floors' => 'integer',
        'year_built' => 'integer',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Furnishing status constants
    const FURNISHING_UNFURNISHED = 'unfurnished';
    const FURNISHING_PARTIALLY = 'partially_furnished';
    const FURNISHING_FULLY = 'fully_furnished';

    // Heating type constants
    const HEATING_CITY_GAS = 'city_gas';
    const HEATING_LPG_COUPONS = 'lpg_with_coupons';
    const HEATING_LPG_NO_COUPONS = 'lpg_without_coupons';
    const HEATING_FUEL = 'fuel_oil';
    const HEATING_ELECTRIC = 'electric';
    const HEATING_HEAT_PUMP = 'heat_pump';
    const HEATING_WOOD = 'wood';
    const HEATING_OTHER = 'other';

    // Heating system constants
    const HEATING_SYSTEM_CENTRALIZED = 'centralized';
    const HEATING_SYSTEM_AUTONOMOUS = 'autonomous';
    const HEATING_SYSTEM_SHARED_US = 'shared_with_us';
    const HEATING_SYSTEM_SHARED_ITALIANS = 'shared_with_italians';

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

    /**
     * Get total number of bathrooms (full + half)
     */
    public function getTotalBathroomsAttribute(): int
    {
        return ($this->full_bathrooms ?? 0) + ($this->half_bathrooms ?? 0);
    }

    /**
     * Calculate distance from Aviano Air Base
     * Base coordinates: 46.031389, 12.596667
     */
    public function calculateDistanceFromBase(): ?float
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $baseLat = 46.031389;
        $baseLng = 12.596667;

        // Haversine formula
        $earthRadius = 6371; // km

        $latDiff = deg2rad($this->latitude - $baseLat);
        $lngDiff = deg2rad($this->longitude - $baseLng);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($baseLat)) * cos(deg2rad($this->latitude)) *
            sin($lngDiff / 2) * sin($lngDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Auto-calculate and save distance from base when coordinates change
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($property) {
            if ($property->isDirty(['latitude', 'longitude'])) {
                $property->distance_from_base_km = $property->calculateDistanceFromBase();
            }

            // Auto-calculate bathrooms for backward compatibility
            if ($property->isDirty(['full_bathrooms', 'half_bathrooms'])) {
                $property->bathrooms = $property->getTotalBathroomsAttribute();
            }
        });
    }
}