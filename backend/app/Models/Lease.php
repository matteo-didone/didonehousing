<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Lease extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'property_id',
        'landlord_id',
        'tenant_id',
        'listing_id',
        'start_date',
        'end_date',
        'duration_years',
        'monthly_rent',
        'security_deposit',
        'condo_fees',
        'status',
        'signed_by_tenant_at',
        'signed_by_landlord_at',
        'approved_by_ho_at',
        'approved_by_ho_id',
        'effective_date',
        'termination_notice_date',
        'termination_date',
        'termination_reason',
        'special_conditions',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'effective_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'condo_fees' => 'decimal:2',
        'signed_by_tenant_at' => 'datetime',
        'signed_by_landlord_at' => 'datetime',
        'approved_by_ho_at' => 'datetime',
        'termination_notice_date' => 'date',
        'termination_date' => 'date',
        'metadata' => 'array',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_SIGNATURES = 'pending_signatures';
    const STATUS_PENDING_HO_APPROVAL = 'pending_ho_approval';
    const STATUS_ACTIVE = 'active';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_EXPIRED = 'expired';

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function approvedByHo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_ho_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_HO_APPROVAL);
    }

    public function isFullySigned(): bool
    {
        return $this->signed_by_tenant_at && $this->signed_by_landlord_at;
    }

    public function signByTenant(): void
    {
        $this->update([
            'signed_by_tenant_at' => now(),
        ]);

        if ($this->isFullySigned()) {
            $this->update(['status' => self::STATUS_PENDING_HO_APPROVAL]);
        }
    }

    public function signByLandlord(): void
    {
        $this->update([
            'signed_by_landlord_at' => now(),
        ]);

        if ($this->isFullySigned()) {
            $this->update(['status' => self::STATUS_PENDING_HO_APPROVAL]);
        }
    }

    public function approveByHo(User $hoApprover): void
    {
        if (!$this->isFullySigned()) {
            throw new \Exception('Lease must be signed by both parties before HO approval');
        }

        $this->update([
            'status' => self::STATUS_ACTIVE,
            'approved_by_ho_at' => now(),
            'approved_by_ho_id' => $hoApprover->id,
            'effective_date' => now()->toDateString(),
        ]);
    }

    public function terminate(string $reason, ?\DateTime $terminationDate = null): void
    {
        $this->update([
            'status' => self::STATUS_TERMINATED,
            'termination_notice_date' => now()->toDateString(),
            'termination_date' => $terminationDate ? $terminationDate->format('Y-m-d') : now()->toDateString(),
            'termination_reason' => $reason,
        ]);
    }
}