<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->setupPermission();
        $this->setupRole();
    }

    public function setupPermission()
    {

        //
        $data = [
            'setting',
            'security_setting'
        ];
        foreach ($data as $permission) {
            Permission::updateOrCreate(['name' => $permission], []);
        }

    }

    public function setupRole()
    {
        //
        $data = [
            'admin',
            'sysadmin'
        ];
        foreach ($data as $role_name) {
            $role = Role::updateOrCreate(['name' => $role_name], []);

            switch ($role_name) {
                case 'admin':
                    $permissions = Permission::wherein('name', ['setting'])->get()->pluck('id', 'id');
                    $role->syncPermissions($permissions);
                    break;
                case 'sysadmin':
                        $permissions = Permission::wherein('name', ['setting', 'security_setting'])->get()->pluck('id', 'id');
                        $role->syncPermissions($permissions);
                        break;
            }
        }
    }

}
