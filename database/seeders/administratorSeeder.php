<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class administratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $in_users = [
            ['id' => '131389',],
            ['id' => '156514',],
            ['id' => '132045',],
            ['id' => '000413',],
            ['id' => '004667',],
            ['id' => '008777',],
            ['id' => '159387',],
            ['id' => '177890',],

        ];

        // Assign Role to User 
        foreach ($in_users as $in_user) {
     
            $user = User::where('emplid',$in_user['id'])->first();

            if ($user) {
                $user->assignRole('admin');

                // Note: is_admin field is used for triggering auditing, have to sync with model_has_roles table
                $user->is_admin = 1;
                $user->save();

            }
        }
    }
}
