<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        'password' => 'employee1@123'
      ],
      [
        'id' => 2,
        'email' => 'employee2@example.com',
        'name' => 'Employee B',
        'password' => 'employee2@123'
      ],
      [
        'id' => 3,
        'email' => 'employee3@example.com',
        'name' => 'Employee C',
        'password' => 'employee3@123'
      ],
      [
        'id' => 999,
        'email' => 'supervisor@example.com',
        'name' => 'Supervisor',
        'password' => 'supervisor@123',
      ]
    ];

        foreach ($users as $user) {
            User::updateOrCreate([
        'email' => $user['email'],
      ], [
        'id' => $user['id'],
        'name' => $user['name'],
        'password' => Hash::make($user['password']),
        'source_type' => 'LCL',
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
