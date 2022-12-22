<?php

namespace Database\Seeders;

use App\Models\FSPool;
use App\Models\Region;
use App\Models\Charity;
use App\Models\FSPoolCharity;
use Illuminate\Database\Seeder;

class FSPoolsJSONSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = database_path('seeds/f_s_pools.json');
        $json = file_get_contents($path);

        $in_pools = json_decode( $json );

        foreach($in_pools as $in_pool ) {

            $region = Region::updateOrCreate([
                            'code' => $in_pool->region->code, 
                        ],[
                            'effdt' => $in_pool->region->effdt, 
                            'name' => $in_pool->region->name, 
                            'status' => $in_pool->region->status, 
                            'notes' => $in_pool->region->notes, 
                            "created_by_id" => $in_pool->region->created_by_id, 
                            "updated_by_id" => $in_pool->region->updated_by_id, 
                            "deleted_at" => $in_pool->region->deleted_at, 
                        ]);

            $pool = FSPool::updateOrCreate([
                            "region_id" => $region->id,
                            "start_date" => $in_pool->start_date,
                        ],[
                            "status"     =>  $in_pool->status,
                            "created_by_id" => $in_pool->created_by_id,
                            "updated_by_id" => $in_pool->updated_by_id,
                            "deleted_at" => $in_pool->deleted_at,
                        ]);

            // Charities
            $pool->charities()->delete();
          
            foreach ($in_pool->charities as $in_pool_charity)  {

                    $charity = Charity::updateOrCreate([
                        "registration_number" => $in_pool_charity->charity->registration_number,
                    ],[
                        "charity_name" => $in_pool_charity->charity->charity_name,
                        "charity_status" => $in_pool_charity->charity->charity_status,
                        "effective_date_of_status" => $in_pool_charity->charity->effective_date_of_status,
                        "sanction" => $in_pool_charity->charity->sanction,
                        "designation_code" => $in_pool_charity->charity->designation_code,
                        "category_code" => $in_pool_charity->charity->category_code,
                        "address" => $in_pool_charity->charity->address,
                        "city" => $in_pool_charity->charity->city,
                        "province" => $in_pool_charity->charity->province,
                        "country" => $in_pool_charity->charity->country,
                        "postal_code" => $in_pool_charity->charity->postal_code,
                        "use_alt_address" => $in_pool_charity->charity->use_alt_address,
                        "alt_address1" => $in_pool_charity->charity->alt_address1,
                        "alt_address2" => $in_pool_charity->charity->alt_address2,
                        "alt_city" => $in_pool_charity->charity->alt_city,
                        "alt_province" => $in_pool_charity->charity->alt_province,
                        "alt_country" => $in_pool_charity->charity->alt_country,
                        "alt_postal_code" => $in_pool_charity->charity->alt_postal_code,
                        "financial_contact_name" => $in_pool_charity->charity->financial_contact_name,
                        "financial_contact_title" => $in_pool_charity->charity->financial_contact_title,
                        "financial_contact_email" => $in_pool_charity->charity->financial_contact_email,
                        "comments" => $in_pool_charity->charity->comments,
                        "ongoing_program" => $in_pool_charity->charity->ongoing_program,
                        "url" => $in_pool_charity->charity->url,
                        "created_by_id" => $in_pool_charity->charity->created_by_id,
                        "updated_by_id" => $in_pool_charity->charity->updated_by_id,
                        // "created_at" => $in_pool_charity->charity-> null,
                        // "updated_at" => $in_pool_charity->charity-> "2022-05-10T09:51:05.000000Z",
                    ]);

                    $pool_charity = FSPoolCharity::create([
                        'f_s_pool_id'   => $pool->id,
                        'charity_id'    => $charity->id,
                        'percentage'    => $in_pool_charity->percentage,
                        'status'        => $in_pool_charity->status,
                        'name'          => $in_pool_charity->name,
                        'description'   => $in_pool_charity->description,
                        'contact_title' => $in_pool_charity->contact_title,
                        'contact_name'  => $in_pool_charity->contact_name,
                        'contact_email' => $in_pool_charity->contact_email,
                        'notes'         => $in_pool_charity->notes,
                        'image'         => $in_pool_charity->image,
                        'deleted_at'    => $in_pool_charity->deleted_at,
                    ]);

            }
        }

    }
}
