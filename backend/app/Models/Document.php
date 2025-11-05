<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Document extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'locale',
        'status',
        'generated_at',
        'approved_at',
        'approved_by_id',
        'metadata',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    const TYPE_LETTER_OF_INTENT = 'letter_of_intent';
    const TYPE_RENTAL_AGREEMENT = 'rental_agreement';
    const TYPE_PROOF_OF_OWNERSHIP = 'proof_of_ownership';
    const TYPE_PAINT_REFUND = 'paint_refund';
    const TYPE_INVENTORY_FORM = 'inventory_form';
    const TYPE_UTILITIES_FORM = 'utilities_form';
    const TYPE_OTHER = 'other';

    const STATUS_DRAFT = 'draft';
    const STATUS_GENERATED = 'generated';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const LOCALE_IT = 'it';
    const LOCALE_EN = 'en';

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::disk('minio')->url($this->file_path);
    }

    public function download()
    {
        return Storage::disk('minio')->download($this->file_path, $this->file_name);
    }

    public function deleteFile(): bool
    {
        return Storage::disk('minio')->delete($this->file_path);
    }

    public function approve(User $approver): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_id' => $approver->id,
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
        ]);
    }

    public static function getTemplatePath(string $type): string
    {
        $templates = [
            self::TYPE_LETTER_OF_INTENT => '01_Letter_of_Intent.pdf',
            self::TYPE_RENTAL_AGREEMENT => '02_Rental_Agreement.pdf',
            self::TYPE_PROOF_OF_OWNERSHIP => '03_Proof_of_Ownership.pdf',
            self::TYPE_PAINT_REFUND => '04_Paint_Refund_Request.pdf',
            self::TYPE_INVENTORY_FORM => '05_Inventory_Form.pdf',
            self::TYPE_UTILITIES_FORM => '06_Utilities_Form.pdf',
        ];

        $filename = $templates[$type] ?? throw new \InvalidArgumentException("Unknown document type: {$type}");

        return storage_path("app/templates/{$filename}");
    }
}