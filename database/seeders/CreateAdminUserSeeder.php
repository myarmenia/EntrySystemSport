<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'armine@webex.am'],
            [
                'name' => 'Admin Adminyan',
                'password' => bcrypt('123456'),
            ]
        );

        $role = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'position_name' => 'super_admin',
                'interface' => 'admin',
            ]
        );

        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        $user->assignRole($role->name);
    }
}
