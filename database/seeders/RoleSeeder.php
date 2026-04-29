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
        if(Role::count()==0)
        {
            Role::factory()->create([
                'title'=>'Super Admin',
                'slug'=>'super-admin',
                'description'=>'Super Admin',
                'is_super'=>'Y',
            ]);

            Role::factory()->create([
                'title'=>'Admin',
                'slug'=>'admin',
                'description'=>'Admin',
                'is_super'=>'N',
            ]);

            Role::factory()->create([
                'title'=>'Manager',
                'slug'=>'manager',
                'description'=>'Manager Role',
                'is_super'=>'N',
            ]);

            Role::factory()->create([
                'title'=>'Customer',
                'slug'=>'customer',
                'description'=>'Customer Role',
                'is_super'=>'N',
            ]);

        }

    }
}
