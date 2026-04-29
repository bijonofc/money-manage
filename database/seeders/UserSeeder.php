<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {

        if ( User::count()==0 ) {
            if ( App::environment( 'production' ) ) {
                User::factory()->create( [
                   'name'      => 'Administrator',
//                   'tenant_id' => 1,
                    'role_id'   => 1,
                    'username'  => 'rootuser',
                    'email'     => 'admin@nogorpos.com',
                    'contact_no' => '+8801613305530',
                    'address'    => 'Bogura,Bangladesh',
                    'password'  => Hash::make('12345')

                ] );
            }else{
                User::factory()->create( [
                    'name'      => 'Super User',
//                'tenant_id' => 1,
                    'role_id'   => 1,
                    'username'  => 'test',
                    'email'     => 'test@example.com',
                    'contact_no' => '+8801721544957',
                    'address'    => 'Dhaka,bangladesh',
                    'password'  => Hash::make('12345')

                ] );
            }

        }
        if ( !App::environment( 'production' ) ) {
            User::factory()->create( [
                'name'      => 'Ad User',
//                'tenant_id' => 1,
                'role_id'   => 2,
                'username'  => 'test2',
                'email'     => 'test2@example.com',
                'contact_no' => '+8801721544958',
                'address'    => 'Dhaka,bangladesh',
                'password'  => Hash::make( '12345' )
            ] );


            User::factory()->create( [
                'name'      => 'Manager',
//                'tenant_id' => 1,
                'role_id'   => 3,
                'username'  => 'manager',
                'email'     => 'manager@example.com',
                'contact_no' => '+8801721544959',
                'address'    => 'Dhaka,bangladesh',
                'password'  => Hash::make( '12345' )
            ] );

            User::factory()->create( [
                'name'      => 'Customer ',
//                'tenant_id' => 1,
                'role_id'   => 4,
                'username'  => 'customer',
                'email'     => 'customer@example.com',
                'contact_no' => '+8801721544960',
                'address'    => 'Dhaka,bangladesh',
                'password'  => Hash::make( '12345' )
            ] );

            User::factory()->create( [
                'name'      => 'Customer 1',
//                'tenant_id' => 1,
                'role_id'   => 4,
                'username'  => 'customer1',
                'email'     => 'customer1@example.com',
                'contact_no' => '+8801721544961',
                'address'    => 'Dhaka,bangladesh',
                'password'  => Hash::make( '12345' )
            ] );
        }
    }
}
