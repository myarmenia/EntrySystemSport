<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'position_name' => 'super_admin',
                'interface' => 'admin'
            ],
            [
                'name' => 'general_manager',
                'position_name' => 'admin',
                'interface' => 'admin'
            ],
            [
                'name' => 'chief_accountant',
                'position_name' => 'admin',
                'interface' => 'admin'
            ],
            [
                'name' => 'head_technic',
                'position_name' => 'admin',
                'interface' => 'admin'
            ],
            [
                'name' => 'client_admin',
                'position_name' => 'admin',
                'interface' => 'client'
            ],
            [
                'name' => 'client_admin_rfID',
                'position_name' => 'admin',
                'interface' => 'client'
            ],
             [
                'name' => 'client_sport',
                'position_name' => 'admin',
                'interface' => 'client'
            ],

            [
                'name' => 'manager',
                'position_name' => 'client',
                'interface' => 'client'
            ],
            [
                'name' => 'accountant',
                'position_name' => 'client',
                'interface' => 'client'
            ],
            [
                'name' => 'technical_manager',
                'position_name' => 'client',
                'interface' => 'client'
            ],
            [
                'name' => 'visitor',
                'position_name' => 'web',
                'interface' => 'web'
            ],
            [
                'name' => 'trainer',
                'position_name' => 'client',
                'interface' => 'client'
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
