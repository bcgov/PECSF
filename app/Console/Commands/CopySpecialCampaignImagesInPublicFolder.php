<?php

namespace App\Console\Commands;

use GuzzleHttp;
use App\Models\FSPoolCharity;
use App\Models\SpecialCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CopySpecialCampaignImagesInPublicFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CopySpecialCampaignImagesInPublicFolder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store the image data in the public directory.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $attachments = SpecialCampaign::whereNotNull('image_data')->orderBy('id')->get();

        foreach ($attachments as $attachment) {

            $filepath = public_path( 'img/uploads/special_campaign/' ) . $attachment->image;
           
            if (file_exists($filepath) ) {
               // Skip 
            } else {
                
                $image_data = base64_decode($attachment->image_data);
                file_put_contents( $filepath, $image_data);

                echo  $attachment->id . ' | ' . $filepath  . PHP_EOL;
            }

        }

        return 0;
    }
}
