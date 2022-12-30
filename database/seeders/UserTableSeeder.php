<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User Creation

        $users = [
      [
        'id' => 1,
        'email' => 'employee1@example.com',
        'name' => 'Employee A',
        'password' => 'employee1@123',
        'emplid' => '130347',

      ],
      [
        'id' => 2,
        'email' => 'employee2@example.com',
        'name' => 'Employee B',
        'password' => 'employee2@123',
        'emplid' => '128932',
      ],
      [
        'id' => 3,
        'email' => 'employee3@example.com',
        'name' => 'Employee C',
        'password' => 'employee3@123',
        'emplid' => '116176',
      ],
      [
        'id' => 999,
        'email' => 'supervisor@example.com',
        'name' => 'Supervisor',
        'password' => 'supervisor@123',
        'emplid' => '112899',

      ]
    ];


        $organization = Organization::where('code','GOV')->first();

        foreach ($users as $user) {
            User::updateOrCreate([
                 'email' => $user['email'],
            ], [
              'id' => $user['id'],
              'name' => $user['name'],
              'password' => Hash::make($user['password']),
              'source_type' => 'LCL',
              'organization_id' => $organization ? $organization->id : null,
              'emplid' => $user['emplid'],
            ]);
        }

      // Assign Role to User 
      $users = User::all();

      foreach ($users as $user) {

        if (str_contains($user->email, 'employee')) {
            // $user->assignRole('employee');
        } elseif (str_contains($user->email, 'supervisor')) {
            //$role = Role::where('name', 'admin')->first();
            $user->assignRole('admin');
        }

      }

    }
}
