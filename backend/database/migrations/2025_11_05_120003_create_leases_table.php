<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();

            // Contract Details
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_years')->default(4);
            $table->decimal('monthly_rent', 10, 2);
            $table->decimal('security_deposit', 10, 2);
            $table->decimal('condo_fees', 10, 2)->nullable();

            // Status & Signatures
            $table->string('status')->default('draft');
            $table->timestamp('signed_by_tenant_at')->nullable();
            $table->timestamp('signed_by_landlord_at')->nullable();
            $table->timestamp('approved_by_ho_at')->nullable();
            $table->foreignId('approved_by_ho_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('effective_date')->nullable();

            // Termination
            $table->date('termination_notice_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();

            // Additional
            $table->text('special_conditions')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('property_id');
            $table->index('landlord_id');
            $table->index('tenant_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};