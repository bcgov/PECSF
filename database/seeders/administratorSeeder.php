<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

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
            ['id' => '131389', 'prod' => 0],
            ['id' => '156514', 'prod' => 1],
            ['id' => '132045', 'prod' => 1],
            ['id' => '000413', 'prod' => 0],
            ['id' => '004667', 'prod' => 0],
            ['id' => '008777', 'prod' => 1],
            ['id' => '159387', 'prod' => 1],
            ['id' => '177890', 'prod' => 1],

        ];

        // Assign Role to User 
        foreach ($in_users as $in_user) {

            if ( (!(App::environment('prod'))) || (App::environment('prod') && $in_user['prod'] == 1)) {            

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
}
