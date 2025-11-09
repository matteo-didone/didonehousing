<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // ============================================================
            // GOOGLE MAPS INTEGRATION
            // ============================================================
            $table->string('google_place_id')->nullable()->after('country');
            $table->decimal('latitude', 10, 8)->nullable()->after('google_place_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->decimal('distance_from_base_km', 5, 2)->nullable()->after('longitude');
            $table->text('formatted_address')->nullable()->after('distance_from_base_km');

            // ============================================================
            // BATHROOMS - Split into full and half
            // ============================================================
            $table->integer('full_bathrooms')->default(0)->after('bedrooms');
            $table->integer('half_bathrooms')->default(0)->after('full_bathrooms');

            // Note: We keep 'bathrooms' column for backward compatibility
            // It will be computed as full_bathrooms + half_bathrooms

            // ============================================================
            // PETS - Add notes field
            // ============================================================
            $table->text('pets_notes')->nullable()->after('pets_allowed');

            // ============================================================
            // HEATING - Detailed system information
            // ============================================================
            // heating_type already exists, but we need to add enum values later
            $table->enum('heating_system', [
                'centralized',
                'autonomous',
                'shared_with_us',
                'shared_with_italians'
            ])->nullable()->after('heating_type');
            $table->boolean('has_heat_meter')->default(false)->after('heating_system');
            $table->text('heating_notes')->nullable()->after('has_heat_meter');

            // ============================================================
            // REDECORATION / PAINT FEES
            // ============================================================
            $table->boolean('redecoration_fees_required')->default(false)->after('heating_notes');
            $table->decimal('redecoration_fees_amount', 10, 2)->nullable()->after('redecoration_fees_required');
            $table->date('redecoration_date')->nullable()->after('redecoration_fees_amount');

            // ============================================================
            // ADDITIONAL PROPERTY DETAILS
            // ============================================================
            $table->integer('floor_number')->nullable()->after('redecoration_date');
            $table->integer('total_floors')->nullable()->after('floor_number');
            $table->boolean('elevator')->default(false)->after('total_floors');
            $table->boolean('balcony')->default(false)->after('elevator');
            $table->boolean('terrace')->default(false)->after('balcony');
            $table->decimal('total_sqm', 8, 2)->nullable()->after('terrace');
            $table->string('energy_class', 5)->nullable()->after('total_sqm');
            $table->integer('year_built')->nullable()->after('energy_class');

            // ============================================================
            // INDEXES for performance
            // ============================================================
            $table->index(['latitude', 'longitude']);
            $table->index('distance_from_base_km');
        });

        // ============================================================
        // DATA MIGRATION: Migrate existing bathrooms to full_bathrooms
        // ============================================================
        DB::statement('UPDATE properties SET full_bathrooms = bathrooms WHERE bathrooms > 0');

        // ============================================================
        // MODIFY EXISTING COLUMNS
        // ============================================================

        // Change 'furnished' from boolean to enum
        // First, create a temporary column
        Schema::table('properties', function (Blueprint $table) {
            $table->enum('furnishing_status', [
                'unfurnished',
                'partially_furnished',
                'fully_furnished'
            ])->default('unfurnished')->after('furnished');
        });

        // Migrate data from furnished to furnishing_status
        DB::statement("
            UPDATE properties
            SET furnishing_status = CASE
                WHEN furnished = TRUE THEN 'fully_furnished'
                ELSE 'unfurnished'
            END
        ");

        // Drop old furnished column
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('furnished');
        });

        // Rename heating_type to be more specific and add enum constraint
        DB::statement("
            ALTER TABLE properties
            MODIFY COLUMN heating_type ENUM(
                'city_gas',
                'lpg_with_coupons',
                'lpg_without_coupons',
                'fuel_oil',
                'electric',
                'heat_pump',
                'wood',
                'other'
            ) NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore furnished boolean column
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('furnished')->default(false)->after('yard');
        });

        // Migrate data back
        DB::statement("
            UPDATE properties
            SET furnished = CASE
                WHEN furnishing_status IN ('partially_furnished', 'fully_furnished') THEN 1
                ELSE 0
            END
        ");

        // Reset heating_type to string
        DB::statement("ALTER TABLE properties MODIFY COLUMN heating_type VARCHAR(255) NULL");

        // Drop all new columns
        Schema::table('properties', function (Blueprint $table) {
            // Google Maps
            $table->dropColumn([
                'google_place_id',
                'latitude',
                'longitude',
                'distance_from_base_km',
                'formatted_address'
            ]);

            // Bathrooms
            $table->dropColumn(['full_bathrooms', 'half_bathrooms']);

            // Pets
            $table->dropColumn('pets_notes');

            // Heating
            $table->dropColumn([
                'heating_system',
                'has_heat_meter',
                'heating_notes'
            ]);

            // Redecoration
            $table->dropColumn([
                'redecoration_fees_required',
                'redecoration_fees_amount',
                'redecoration_date'
            ]);

            // Additional details
            $table->dropColumn([
                'floor_number',
                'total_floors',
                'elevator',
                'balcony',
                'terrace',
                'total_sqm',
                'energy_class',
                'year_built',
                'furnishing_status'
            ]);
        });
    }
};
