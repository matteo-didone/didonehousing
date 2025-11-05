<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');

            // Address
            $table->string('street_name');
            $table->string('house_number');
            $table->string('apt_number')->nullable();
            $table->string('city');
            $table->string('province', 10);
            $table->string('postal_code', 20);
            $table->string('country', 2)->default('IT');

            // Cadastral Data
            $table->string('cadastral_sheet_number')->nullable();
            $table->string('cadastral_plot_number')->nullable();
            $table->string('cadastral_unit_number')->nullable();
            $table->string('cadastral_tax_evaluation')->nullable();
            $table->string('cadastral_category')->nullable();

            // Rooms
            $table->integer('living_rooms')->default(0);
            $table->integer('dining_rooms')->default(0);
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('kitchen')->default(0);
            $table->boolean('basement')->default(false);
            $table->boolean('attic')->default(false);
            $table->boolean('garage')->default(false);
            $table->boolean('yard')->default(false);

            // Property Details
            $table->boolean('furnished')->default(false);
            $table->boolean('pets_allowed')->default(false);
            $table->string('heating_type')->nullable();
            $table->string('cooling_type')->nullable();

            // Status & HO Review
            $table->string('status')->default('draft');
            $table->timestamp('ho_reviewed_at')->nullable();
            $table->foreignId('ho_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ho_comments')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('landlord_id');
            $table->index('status');
            $table->index(['city', 'province']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};