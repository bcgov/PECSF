<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\FSPoolCharity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class FSPoolCharityImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $attachments = FSPoolCharity::whereNull('image_data')->orderBy('id')->get();

        foreach ($attachments as $attachment) {

            $filename = $attachment->image;
            $filepath = public_path( 'img/uploads/fspools/' ) . $filename;
            // $original_filename = substr($filename, strpos($filename, '_') + 1);
            $mime = pathinfo( $filepath , PATHINFO_EXTENSION);

            $doc = file_get_contents( $filepath );
            $base64 = base64_encode($doc);

            // File::move( storage_path( 'app/tmp/'. $filename ), storage_path( $this->doc_folder ."/". $filename));

            // echo $attachment->local_path; 
            $attachment->mime = $mime;
            $attachment->image_data = $base64;
            $attachment->timestamps = false;
            $attachment->save();

            echo  $attachment->id . ' | ' . $filepath  . ' | ' . $mime . PHP_EOL;

        }

    }

}
