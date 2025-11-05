<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Listing extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'property_id',
        'monthly_rent',
        'security_deposit',
        'condo_fees',
        'duration_years',
        'status',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'published_at',
        'checklist_data',
        'ho_comments',
        'ho_reviewer_id',
    ];

    protected $casts = [
        'monthly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'condo_fees' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
        'checklist_data' => 'array',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PUBLISHED = 'published';
    const STATUS_UNPUBLISHED = 'unpublished';

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function hoReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ho_reviewer_id');
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

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeInReview($query)
    {
        return $query->where('status', self::STATUS_IN_REVIEW);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function submit(): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function startReview(User $hoReviewer): void
    {
        $this->update([
            'status' => self::STATUS_IN_REVIEW,
            'ho_reviewer_id' => $hoReviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    public function approve(User $hoReviewer, ?string $comments = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'ho_reviewer_id' => $hoReviewer->id,
            'ho_comments' => $comments,
            'approved_at' => now(),
        ]);
    }

    public function reject(User $hoReviewer, string $comments): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'ho_reviewer_id' => $hoReviewer->id,
            'ho_comments' => $comments,
        ]);
    }

    public function publish(): void
    {
        if ($this->status !== self::STATUS_APPROVED) {
            throw new \Exception('Listing must be approved before publishing');
        }

        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }
}