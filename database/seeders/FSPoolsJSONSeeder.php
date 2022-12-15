<?php

namespace Database\Seeders;

use App\Models\FSPool;
use App\Models\Region;
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
    
                    $pool_charity = FSPoolCharity::create([
                        'f_s_pool_id'   => $in_pool_charity->f_s_pool_id,
                        'charity_id'    => $in_pool_charity->charity_id,
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
