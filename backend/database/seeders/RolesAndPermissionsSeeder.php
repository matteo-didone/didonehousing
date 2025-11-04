<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage-roles',

            // Property Management
            'properties.view',
            'properties.view-own',
            'properties.create',
            'properties.edit',
            'properties.edit-own',
            'properties.delete',
            'properties.delete-own',
            'properties.approve',
            'properties.reject',

            // Listing Management
            'listings.view',
            'listings.view-own',
            'listings.create',
            'listings.edit',
            'listings.edit-own',
            'listings.publish',
            'listings.unpublish',

            // Application Management
            'applications.view',
            'applications.view-own',
            'applications.create',
            'applications.review',
            'applications.approve',
            'applications.reject',

            // Lease/Contract Management
            'leases.view',
            'leases.view-own',
            'leases.create',
            'leases.edit',
            'leases.sign',
            'leases.approve',
            'leases.terminate',

            // Payment Management
            'payments.view',
            'payments.view-own',
            'payments.create',
            'payments.process',
            'payments.refund',

            // Invoice Management
            'invoices.view',
            'invoices.view-own',
            'invoices.create',
            'invoices.send',
            'invoices.cancel',

            // Deposit Management
            'deposits.view',
            'deposits.view-own',
            'deposits.manage',
            'deposits.release',

            // Maintenance/Ticket Management
            'tickets.view',
            'tickets.view-own',
            'tickets.create',
            'tickets.assign',
            'tickets.update',
            'tickets.close',

            // Work Order Management
            'work-orders.view',
            'work-orders.view-own',
            'work-orders.create',
            'work-orders.accept',
            'work-orders.complete',
            'work-orders.invoice',

            // Document Management
            'documents.view',
            'documents.view-own',
            'documents.upload',
            'documents.download',
            'documents.delete',
            'documents.approve',

            // Messaging
            'messages.view',
            'messages.view-own',
            'messages.send',

            // Reports & Analytics
            'reports.view',
            'reports.export',
            'reports.landlord',
            'reports.tenant',
            'reports.ho',
            'reports.financial',

            // System Administration
            'system.settings',
            'system.audit-logs',
            'system.maintenance',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. ADMIN - Full system access
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // 2. TENANT - Can search, apply, rent, pay, request maintenance
        $tenant = Role::create(['name' => 'tenant']);
        $tenant->givePermissionTo([
            'properties.view',
            'listings.view',
            'applications.create',
            'applications.view-own',
            'leases.view-own',
            'leases.sign',
            'payments.view-own',
            'payments.create',
            'invoices.view-own',
            'deposits.view-own',
            'tickets.create',
            'tickets.view-own',
            'documents.view-own',
            'documents.upload',
            'documents.download',
            'messages.view-own',
            'messages.send',
            'reports.tenant',
        ]);

        // 3. LANDLORD - Can manage properties, review applications, manage leases
        $landlord = Role::create(['name' => 'landlord']);
        $landlord->givePermissionTo([
            'properties.view-own',
            'properties.create',
            'properties.edit-own',
            'properties.delete-own',
            'listings.view-own',
            'listings.create',
            'listings.edit-own',
            'listings.publish',
            'listings.unpublish',
            'applications.view',
            'applications.review',
            'applications.approve',
            'applications.reject',
            'leases.view-own',
            'leases.create',
            'leases.edit',
            'leases.sign',
            'payments.view-own',
            'invoices.view-own',
            'invoices.create',
            'deposits.view-own',
            'deposits.manage',
            'tickets.view',
            'tickets.assign',
            'tickets.update',
            'work-orders.create',
            'documents.view-own',
            'documents.upload',
            'documents.download',
            'messages.view-own',
            'messages.send',
            'reports.landlord',
        ]);

        // 4. HOUSING OFFICE (HO) - Can approve properties, review leases, mediate disputes
        $ho = Role::create(['name' => 'ho']);
        $ho->givePermissionTo([
            'properties.view',
            'properties.approve',
            'properties.reject',
            'listings.view',
            'applications.view',
            'leases.view',
            'leases.approve',
            'payments.view',
            'invoices.view',
            'deposits.view',
            'deposits.release',
            'tickets.view',
            'tickets.update',
            'work-orders.view',
            'documents.view',
            'documents.approve',
            'messages.view',
            'messages.send',
            'reports.view',
            'reports.ho',
            'reports.export',
        ]);

        // 5. VENDOR - Can receive and complete work orders
        $vendor = Role::create(['name' => 'vendor']);
        $vendor->givePermissionTo([
            'tickets.view-own',
            'tickets.update',
            'work-orders.view-own',
            'work-orders.accept',
            'work-orders.complete',
            'work-orders.invoice',
            'documents.upload',
            'documents.view-own',
            'messages.view-own',
            'messages.send',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created roles: admin, tenant, landlord, ho, vendor');
        $this->command->info('Created ' . count($permissions) . ' permissions');
    }
}
