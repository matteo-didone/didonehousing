<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');

            // Financial Details
            $table->decimal('monthly_rent', 10, 2);
            $table->decimal('security_deposit', 10, 2);
            $table->decimal('condo_fees', 10, 2)->nullable();
            $table->integer('duration_years')->default(4);

            // Status & Workflow
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();

            // HO Review
            $table->json('checklist_data')->nullable();
            $table->text('ho_comments')->nullable();
            $table->foreignId('ho_reviewer_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('property_id');
            $table->index('status');
            $table->index('ho_reviewer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};