<?php

namespace App\Console\Commands;

use GuzzleHttp;
use App\Models\FSPoolCharity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DownloadImageFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DownloadImageFiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

//  dd( Storage::disk('images') );
        // $filenames = [
        //     '20220610160012_awkward-seal.jpg',
        //     '20220614110441_GadsbyRockStar.png',
        //     '20220614110441_Hamlet.jpg',
        //     '20220616081031_PECSF_2_line_logo_gradient.png',
        //     '20220616101338_PECSF_logo_blue_Trans_BG.png',
        //     '20220620114433_PECSF_Icon.png',
        //     '20220620122520_AutismBC-Primary_Logo-4C.jpg',
        //     '20220620122520_Junior-Achievement-BC.jpg',
        //     '20220620122520_PECSF_Icon.png',
        //     '20220620123321_HopeAir.png',
        //     '20220620123321_PECSF_Icon.png',
        //     '20220620123321_sal_army.png',
        //     '20220620123744_AutismBC-Primary_Logo-4C.jpg',
        //     '20220620123744_Junior-Achievement-BC.jpg',
        //     '20220721175534_BC1.png',
        //     '20220808110024_AutismBC-Primary_Logo-4C.jpg',
        //     '20220808110024_Junior-Achievement-BC.jpg',
        //     '20220808110024_PECSF_2_line_logo_blue.png',
        //     '20220808141403_AutismSocietyBC.png',
        //     '20220808141403_Junior-Achievement-BC.jpg',
        //     '20220808142002_HopeAir.png',
        //     '20220808142002_sal_army.png',
        //     '20220809141140_Screen_Shot_2022-08-09_at_4.40.59_PM.png',
        //     '20220809142313_Screen_Shot_2022-08-09_at_3.56.15_PM.png',
        //     '2022081008341240_Screen_Shot_2022-08-09_at_4.40.59_PM.png',
        //     '20220810084625_pecsf-logo.png',
        //     '20220810192630_BC-government-logo-NEW-768x728.jpeg',
        //     '2022081212232025_pecsf-logo.png',
        //     '2022081214115440_Screen_Shot_2022-08-09_at_4.40.59_PM.png',
        //     '2022081214124534_BC1.png',
        //     '202208151216522025_pecsf-logo.png',
        //     '20220815122349_Screen_Shot_2022-08-10_at_4.50.26_PM.png',
        //     '20220815122801_Screen_Shot_2022-08-10_at_4.50.26_PM.png',
        //     '20220815130501_Screen_Shot_2022-08-12_at_9.44.11_AM.png',
        //     '20220815130850_Screen_Shot_2022-08-12_at_9.44.07_AM.png',
        //     '20220815130850_Screen_Shot_2022-08-12_at_9.44.11_AM.png',
        //     '20220815131012_Screen_Shot_2022-08-12_at_9.44.11_AM.png',
        //     '202208151310512025_pecsf-logo.png',
        //     '20220819094942_BC_wildlife_logo.jpg',
        //     '20220819094942_SouthIslandCentreforTrain.jpg',
        //     '20220819094942_habitatforhumanity.jpg',
        //     '2022081915322220_AutismBC-Primary_Logo-4C.jpg',
        //     '2022081915322220_Junior-Achievement-BC.jpg',
        //     '2022081915322220_PECSF_Icon.png',
        //     '20220819153424522025_pecsf-logo.png',
        //     '20220819154734522025_pecsf-logo.png',
        //     '2022081915475734522025_pecsf-logo.png',
        // ];

        // Based on the FS Pool Charity''s image listing
        $filenames = FSPoolCharity::select('image')->distinct()->pluck('image');

        $baseUrl = env('IMAGES_SOURCE_URL');

        // $client->request('GET', '/stream/20', ['sink' => '/path/to/file']);

        foreach($filenames as $filename) {

            echo $filename . PHP_EOL;

            $client = new GuzzleHttp\Client();
            
            // check the file exists or not 
            try {
                $res = $client->request('GET',  $baseUrl . $filename,
                    [
                        'headers' => [
                            'Accept-Encoding' => 'gzip, deflate, br', //add encoding technique
                        ],
                    ]
                );

                if ($res->getStatusCode() == 200 ) {
                    $res = $client->request('GET',  $baseUrl . $filename, 
                        ['sink' => 'public/img/uploads/fspools/' . $filename ]
                    );
                }
    
            } catch (GuzzleHttp\Exception\RequestException $e) {
                echo 'Uh oh! ' . $e->getMessage();
            }
            

        }


        // Alternative way but require config filesystems.php 
        // 
        // 'images' => [
        //     'driver' => 'local',
        //     'root'   => public_path() . '/img/uploads',
        //     'url' => env('APP_URL').'/public',
        //     'visibility' => 'public',
        // ],
        //
        // foreach($filenames as $filename) {
        //     echo $filename . PHP_EOL;
        //     Storage::disk('images')->put($filename, file_get_contents( $baseUrl . $filename));
        // }
        


        return 0;
    }
}
