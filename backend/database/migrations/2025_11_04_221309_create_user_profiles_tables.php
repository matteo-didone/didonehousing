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
        // Tenant Profiles
        Schema::create('tenant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('rank')->nullable(); // Military rank
            $table->string('branch')->nullable(); // Air Force, Army, etc.
            $table->string('unit')->nullable();
            $table->string('sponsor_name')->nullable();
            $table->string('sponsor_phone')->nullable();
            $table->date('pcs_date')->nullable(); // Permanent Change of Station date
            $table->date('deros_date')->nullable(); // Date Eligible for Return from Overseas
            $table->integer('family_size')->default(1);
            $table->boolean('has_pets')->default(false);
            $table->text('pet_details')->nullable();
            $table->decimal('oha_amount', 10, 2)->nullable(); // Overseas Housing Allowance
            $table->string('oha_currency', 3)->default('USD');
            $table->text('special_requirements')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Landlord Profiles
        Schema::create('landlord_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable(); // Codice Fiscale / P.IVA
            $table->string('business_type')->nullable(); // individual, company
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province', 2)->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->default('IT');
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_bic')->nullable();
            $table->boolean('cedolare_secca')->default(false); // Flat-rate tax option
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Housing Office Profiles
        Schema::create('ho_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('office_name')->default('Aviano Housing Office');
            $table->string('employee_id')->nullable();
            $table->string('department')->nullable();
            $table->string('phone_extension')->nullable();
            $table->boolean('can_approve_properties')->default(true);
            $table->boolean('can_approve_contracts')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Vendor Profiles
        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('tax_id'); // P.IVA
            $table->text('services_offered'); // JSON array of service categories
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province', 2)->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->default('IT');
            $table->text('service_area')->nullable(); // JSON array of cities/areas
            $table->string('website')->nullable();
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('total_jobs')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->boolean('emergency_available')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_profiles');
        Schema::dropIfExists('ho_profiles');
        Schema::dropIfExists('landlord_profiles');
        Schema::dropIfExists('tenant_profiles');
    }
};
