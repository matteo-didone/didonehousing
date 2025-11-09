<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Garage type: indoor (interno), outdoor (esterno), both (entrambi)
            $table->enum('garage_type', ['indoor', 'outdoor', 'both'])
                ->nullable()
                ->after('garage');

            // Number of garage spaces
            $table->integer('garage_spaces')->default(0)->after('garage_type');

            // Yard/Garden type: front, back, both
            $table->enum('yard_type', ['front', 'back', 'both'])
                ->nullable()
                ->after('yard');

            // Yard size in square meters
            $table->decimal('yard_sqm', 8, 2)->nullable()->after('yard_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'garage_type',
                'garage_spaces',
                'yard_type',
                'yard_sqm'
            ]);
        });
    }
};
