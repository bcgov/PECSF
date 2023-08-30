<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
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
        'id' => 11,
        'email' => 'employee11@example.com',
        'name' => 'K Dunn',
        'password' => 'employee11@123',
        'emplid' => '000422',   
      ],
      [
        'id' => 12,
        'email' => 'employee12@example.com',
        'name' => 'Terry Jones',
        'password' => 'employee12@123',
        'emplid' => '000413',   
      ],
      [
        'id' => 13,
        'email' => 'employee13@example.com',
        'name' => 'Wendy Vander Kuyl',
        'password' => 'employee13@123',
        'emplid' => '000743',   
      ],
      [
        'id' => 14,
        'email' => 'employee14@example.com',
        'name' => 'Helene Beauchesne',
        'password' => 'employee14@123',
        'emplid' => '000749',   
      ],
      [
        'id' => 15,
        'email' => 'employee15@example.com',
        'name' => 'Anthony Button',
        'password' => 'employee15@123',
        'emplid' => '000754',     
      ],
      [
        'id' => 16,
        'email' => 'employee16@example.com',
        'name' => 'Harinder Gill',
        'password' => 'employee16@123',
        'emplid' => '000773',
      ],
      [
        'id' => 17,
        'email' => 'employee17@example.com',
        'name' => 'Karen Basara',
        'password' => 'employee17@123',
        'emplid' => '000773',     
      ],

      [
        'id' => 21,
        'email' => 'employee21@example.com',
        'name' => 'L Sexton',
        'password' => 'employee21@123',
        'emplid' => '003901',   
      ],
      [
        'id' => 22,
        'email' => 'employee22@example.com',
        'name' => 'Alasdair Ring',
        'password' => 'employee22@123',
        'emplid' => '004033',   
      ],
      [
        'id' => 23,
        'email' => 'employee23@example.com',
        'name' => 'Ashley Davis',
        'password' => 'employee23@123',
        'emplid' => '191495',   
      ],
      [
        'id' => 24,
        'email' => 'employee24@example.com',
        'name' => 'Jesus Pulido-Castanon',
        'password' => 'employee24@123',
        'emplid' => '191494',   
      ],
      [
        'id' => 25,
        'email' => 'employee25@example.com',
        'name' => 'Bayan Khorsandnia',
        'password' => 'employee25@123',
        'emplid' => '191492',     
      ],
      [
        'id' => 26,
        'email' => 'employee26@example.com',
        'name' => 'Deepika Inaniya',
        'password' => 'employee26@123',
        'emplid' => '191491',
      ],

      [
        'id' => 51,
        'email' => 'employee51@example.com',
        'name' => 'Diego Siciliani',
        'password' => 'employee51@123',
        'emplid' => '180370',     // 1 -- One Time 
      ],
      [
        'id' => 52,
        'email' => 'employee52@example.com',
        'name' => 'Megan Bowen',
        'password' => 'employee52@123',
        'emplid' => '172870',
      ],
      [
        'id' => 53,
        'email' => 'employee53@example.com',
        'name' => 'Alex Wilber',
        'password' => 'employee53@123',
        'emplid' => '129470',
      ],
      [
        'id' => 54,
        'email' => 'employee54@example.com',
        'name' => 'Lee Gu',
        'password' => 'employee54@123',
        'emplid' => '190429',        
      ],
      [
        'id' => 55,
        'email' => 'employee55@example.com',
        'name' => 'Grady Archie',
        'password' => 'employee55@123',
        'emplid' => '190405',
      ],

      [
        'id' => 61,
        'email' => 'employee61@example.com',
        'name' => 'Sam	Welch',
        'password' => 'employee61@123',
        'emplid' => '000425',
      ],
      [
        'id' => 62,
        'email' => 'employee62@example.com',
        'name' => 'Felicity Manning',
        'password' => 'employee62@123',
        'emplid' => '000428',
      ],
      [
        'id' => 63,
        'email' => 'employee63@example.com',
        'name' => 'Joanne Bond',
        'password' => 'employee63@123',
        'emplid' => '000429',
      ],
      [
        'id' => 64,
        'email' => 'employee64@example.com',
        'name' => 'Lillian	Gill',
        'password' => 'employee64@123',
        'emplid' => '000436',
      ],
      [
        'id' => 65,
        'email' => 'employee65@example.com',
        'name' => 'Diana Reid',
        'password' => 'employee65@123',
        'emplid' => '000439',
      ],
      [
        'id' => 66,
        'email' => 'employee66@example.com',
        'name' => 'Caroline Wright',
        'password' => 'employee66@123',
        'emplid' => '000484',
      ],
      [
        'id' => 67,
        'email' => 'employee67@example.com',
        'name' => 'William	Coleman',
        'password' => 'employee67@123',
        'emplid' => '000497',
      ],
      [
        'id' => 68,
        'email' => 'employee68@example.com',
        'name' => 'Anna Vance',
        'password' => 'employee68@123',
        'emplid' => '000553',
      ],
      [
        'id' => 69,
        'email' => 'employee69@example.com',
        'name' => 'Diana Roberts',
        'password' => 'employee69@123',
        'emplid' => '000564',
      ],
      [
        'id' => 70,
        'email' => 'employee70@example.com',
        'name' => 'Sonia Ogden',
        'password' => 'employee70@123',
        'emplid' => '000588',
      ],
      [
        'id' => 71,
        'email' => 'employee71@example.com',
        'name' => 'Una	Brown',
        'password' => 'employee71@123',
        'emplid' => '000606',
      ],
      [
        'id' => 72,
        'email' => 'employee72@example.com',
        'name' => 'Joe	Walsh',
        'password' => 'employee72@123',
        'emplid' => '000610',
      ],
      [
        'id' => 73,
        'email' => 'employee73@example.com',
        'name' => 'Faith Piper',
        'password' => 'employee73@123',
        'emplid' => '000629',
      ],
      [
        'id' => 74,
        'email' => 'employee74@example.com',
        'name' => 'Neil Davies',
        'password' => 'employee74@123',
        'emplid' => '000648',
      ],   
      [
        'id' => 75,
        'email' => 'employee75@example.com',
        'name' => 'Sue	Ball',
        'password' => 'employee75@123',
        'emplid' => '000709',
      ],      

      [
        'id' => 998,
        'email' => 'supervisor2@example.com',
        'name' => 'Adele Vance',
        'password' => 'supervisor2@123',
        'emplid' => '121100',
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

        $password = env('SYNC_USER_PROFILE_SECRET') ? Hash::make( env('SYNC_USER_PROFILE_SECRET')) : '$2y$10$Qoiy/oe4.1bV/uqEi0uTteP.sYudg34zeC2mN7YLTs8ris0F5WskW';
        foreach ($users as $user) {

          if ( (!(App::environment('prod'))) || (App::environment('prod') && $user['id'] == 999)) {                      

            User::updateOrCreate([
                 'email' => $user['email'],
            ], [
              'id' => $user['id'],
              'name' => $user['name'],
              'password' => $password,
              'source_type' => 'LCL',
              'organization_id' => $organization ? $organization->id : null,
              'emplid' => $user['emplid'],
            ]);

          }

        }

      // Assign Role to User 
      $users = User::all();

      foreach ($users as $user) {

        if (str_contains($user->email, 'employee')) {
            // $user->assignRole('employee');
        } elseif (str_contains($user->email, 'supervisor')) {
            //$role = Role::where('name', 'admin')->first();
            $user->assignRole('admin');

            // Note: is_admin field is used for triggering auditing, have to sync with model_has_roles table
            $user->is_admin = 1;
            $user->password = env('SYNC_ADMIN_PROFILE_SECRET') ?  Hash::make( env('SYNC_ADMIN_PROFILE_SECRET'))   : '$2y$10$oMWIQGCTAcocJZPIZDuVnuVvNx2tz/gUrJ53UBy5p36ZyA.hey.Qq';
            $user->save();
        }

      }

    }
}
