<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BankDepositFormAttachments;

class BankDepositFormAttachmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $attachemnts = BankDepositFormAttachments::whereNull('file')->where('local_path','like', 'C:%')->orderBy('id')->get();

        foreach ($attachemnts as $attachment) {

            

            $filename = substr($attachment->local_path, strrpos($attachment->local_path, '/') + 1);
            $original_filename = substr($filename, strpos($filename, '_') + 1);
            $mime = pathinfo( storage_path( $attachment->local_path ), PATHINFO_EXTENSION);
            echo $filename . ' | ' . $original_filename . ' | ' . $mime . PHP_EOL;

            $doc = file_get_contents( $attachment->local_path );
            $base64 = base64_encode($doc);

            // File::move( storage_path( 'app/tmp/'. $filename ), storage_path( $this->doc_folder ."/". $filename));

            // echo $attachment->local_path; 
            $attachment->filename = $filename;
            $attachment->original_filename = $original_filename;
            $attachment->mime = $mime;
            $attachment->file = $base64;
            $attachment->save();

        }

    }

}
