<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TenantProfile;
use App\Models\LandlordProfile;
use App\Models\HoProfile;
use App\Models\VendorProfile;
use Illuminate\Support\Facades\Hash;

class DevelopmentUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@avianohousing.local',
            'phone' => '+39 0434 123456',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'locale' => 'en',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');
        $this->command->info('Created admin user: admin@avianohousing.local');

        // 2. Tenant User (Military Personnel)
        $tenant = User::create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'tenant@avianohousing.local',
            'phone' => '+1 555 0100',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'locale' => 'en',
            'is_active' => true,
        ]);
        $tenant->assignRole('tenant');
        TenantProfile::create([
            'user_id' => $tenant->id,
            'rank' => 'SSgt',
            'branch' => 'Air Force',
            'unit' => '31st Fighter Wing',
            'sponsor_name' => 'Jane Smith',
            'sponsor_phone' => '+1 555 0101',
            'pcs_date' => now()->addMonths(1),
            'deros_date' => now()->addYears(3),
            'family_size' => 4,
            'has_pets' => true,
            'pet_details' => '1 dog (Labrador, 30kg)',
            'oha_amount' => 2500.00,
            'oha_currency' => 'USD',
            'special_requirements' => 'Need ground floor due to mobility issues',
        ]);
        $this->command->info('Created tenant user: tenant@avianohousing.local');

        // 3. Landlord User
        $landlord = User::create([
            'first_name' => 'Marco',
            'last_name' => 'Rossi',
            'email' => 'landlord@avianohousing.local',
            'phone' => '+39 333 1234567',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'locale' => 'it',
            'is_active' => true,
        ]);
        $landlord->assignRole('landlord');
        LandlordProfile::create([
            'user_id' => $landlord->id,
            'company_name' => 'Rossi Immobiliare SRL',
            'tax_id' => 'IT12345678901',
            'business_type' => 'company',
            'address' => 'Via Roma 123',
            'city' => 'Aviano',
            'province' => 'PN',
            'postal_code' => '33081',
            'country' => 'IT',
            'bank_name' => 'Intesa Sanpaolo',
            'iban' => 'IT60X0542811101000000123456',
            'swift_bic' => 'BCITITMMXXX',
            'cedolare_secca' => true,
            'is_verified' => true,
            'verified_at' => now(),
        ]);
        $this->command->info('Created landlord user: landlord@avianohousing.local');

        // 4. Housing Office User
        $ho = User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'ho@avianohousing.local',
            'phone' => '+39 0434 667890',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'locale' => 'en',
            'is_active' => true,
        ]);
        $ho->assignRole('ho');
        HoProfile::create([
            'user_id' => $ho->id,
            'office_name' => 'Aviano Housing Office',
            'employee_id' => 'AHO-001',
            'department' => 'Housing Management',
            'phone_extension' => '1234',
            'can_approve_properties' => true,
            'can_approve_contracts' => true,
        ]);
        $this->command->info('Created housing office user: ho@avianohousing.local');

        // 5. Vendor User
        $vendor = User::create([
            'first_name' => 'Giuseppe',
            'last_name' => 'Bianchi',
            'email' => 'vendor@avianohousing.local',
            'phone' => '+39 348 7654321',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'locale' => 'it',
            'is_active' => true,
        ]);
        $vendor->assignRole('vendor');
        VendorProfile::create([
            'user_id' => $vendor->id,
            'company_name' => 'Bianchi Plumbing & Heating',
            'tax_id' => 'IT98765432109',
            'services_offered' => ['plumbing', 'heating', 'electrical'],
            'address' => 'Via Dante 45',
            'city' => 'Pordenone',
            'province' => 'PN',
            'postal_code' => '33170',
            'country' => 'IT',
            'service_area' => ['Aviano', 'Pordenone', 'Roveredo in Piano'],
            'website' => 'https://bianchiplumbing.it',
            'average_rating' => 4.8,
            'total_jobs' => 156,
            'is_verified' => true,
            'verified_at' => now(),
            'emergency_available' => true,
        ]);
        $this->command->info('Created vendor user: vendor@avianohousing.local');

        $this->command->info('');
        $this->command->info('=================================================');
        $this->command->info('Development users created successfully!');
        $this->command->info('=================================================');
        $this->command->info('All users have password: password');
        $this->command->info('');
        $this->command->info('Admin:    admin@avianohousing.local');
        $this->command->info('Tenant:   tenant@avianohousing.local');
        $this->command->info('Landlord: landlord@avianohousing.local');
        $this->command->info('HO:       ho@avianohousing.local');
        $this->command->info('Vendor:   vendor@avianohousing.local');
        $this->command->info('=================================================');
    }
}
