<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Academic module
            'academic.academic_terms.view',
            'academic.academic_terms.create',
            'academic.academic_terms.edit',
            'academic.academic_terms.delete',
            'academic.faculties.view',
            'academic.faculties.create',
            'academic.faculties.edit',
            'academic.faculties.delete',
            'academic.programs.view',
            'academic.programs.create',
            'academic.programs.edit',
            'academic.programs.delete',
            // Housing Management module
            'housing.buildings.view',
            'housing.buildings.create',
            'housing.buildings.edit',
            'housing.buildings.delete',
            'housing.apartments.view',
            'housing.apartments.create',
            'housing.apartments.edit',
            'housing.apartments.delete',
            'housing.rooms.view',
            'housing.rooms.create',
            'housing.rooms.edit',
            'housing.rooms.delete',
            // Residents module
            'resident.students.view',
            'resident.students.create',
            'resident.students.edit',
            'resident.students.delete',
            'resident.staff.view',
            'resident.staff.create',
            'resident.staff.edit',
            'resident.staff.delete',
            // Reservations module
            'reservations.view',
            'reservations.create',
            'reservations.edit',
            'reservations.delete',
            'reservations.check_in_out',
            'reservation_requests.view',
            'reservation_requests.approve',
            // Payments module
            'payments.view',
            'payments.create',
            'payments.edit',
            'insurances.view',
            'insurances.create',
            'insurances.edit',
            // Dashboard
            'dashboard.view',
            // Home
            'home.view',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);

        $resident = Role::firstOrCreate(['name' => 'resident']);

        // Assign permissions to admin (all except 'home.view')
        $adminPermissions = array_filter($permissions, function ($perm) {
            return $perm !== 'home.view';
        });

        $admin->syncPermissions($adminPermissions);

        // Assign only 'home.view' to resident
        $resident->syncPermissions(['home.view']);
    }
}