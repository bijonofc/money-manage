<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Role::count() == 0) {
            Role::create([
                'title' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Super Admin',
                'is_super' => 'Y',
            ]);

            Role::create([
                'title' => 'Admin',
                'slug' => 'admin',
                'description' => 'Admin',
                'is_super' => 'N',
            ]);

            Role::create([
                'title' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager Role',
                'is_super' => 'N',
            ]);

            Role::create([
                'title' => 'Customer',
                'slug' => 'customer',
                'description' => 'Customer Role',
                'is_super' => 'N',
            ]);

            // Seed default permissions for Admin role (role_id = 2)
            $adminPermissions = [
                'user-list', 'user-add', 'user-edit',
                'role-list',
                'account-list', 'account-add', 'account-edit',
                'transaction-list', 'transaction-add', 'transaction-edit', 'transaction-delete',
                'budget-list', 'savings-list', 'debt-list', 'category-list', 'setting-view',
            ];
            foreach ($adminPermissions as $perm) {
                \App\Models\RoleAccess::create([
                    'role_id'     => 2,
                    'resource'    => $perm,
                    'role_access' => 'Y',
                ]);
            }

            // Seed default permissions for Manager role (role_id = 3)
            $managerPermissions = [
                'account-list', 'account-add', 'account-edit',
                'transaction-list', 'transaction-add', 'transaction-edit',
                'budget-list', 'savings-list', 'debt-list', 'category-list',
            ];
            foreach ($managerPermissions as $perm) {
                \App\Models\RoleAccess::create([
                    'role_id'     => 3,
                    'resource'    => $perm,
                    'role_access' => 'Y',
                ]);
            }
        }

    }
}
