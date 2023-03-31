<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use Illuminate\Database\Seeder;
use App\Models\ElligibleEmployee;
use App\Models\EligibleEmployeeDetail;

class EligibleEmployeeCount2023Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data_2021 = [
                ['BC019',441,'Adv Ed, Skills & Training'],
                ['BC130',449,'Agriculture, Food & Fisheries'],
                ['BC105',4867,'Attorney General'],
                ['BC131',0,'Attorney General - Housing'],
                ['BC088',629,'BC Pension Corp'],
                ['BC100',570,'BC Public Service Agency'],
                ['BC109',78,'BC Rep for Children & Youth'],
                ['BC039',4794,'Children & Family Development'],
                ['BC067',2242,'Citizens Services'],
                ['BC112',0,'Citizens Services'],
                ['BC805',697,'Community Living BC'],
                ['BC005',3,'Conflict of Interest Commissr'],
                ['BC825',106,'Destination BC Corp.'],
                ['BC015',61,'Elections BC'],
                ['BC057',430,'Energy, Mines & Low Carb Inn.'],
                ['BC048',1402,'Env & Climate Change Strategy'],
                ['BC115',96,'Env Assessment Office'],
                ['BC128',5037,'FLNRO and Rural Development'],
                ['BC079',19,'Forest Practices Board'],
                ['BC026',1609,'Health'],
                ['BC113',28,'Human Rights Commissioner'],
                ['BC120',249,'Indigenous Relations & Recon'],
                ['BC125',284,'Jobs, Economic Recovery & Inn'],
                ['BC029',86,'Mental Health & Addictions'],
                ['BC034',1585,'Min of Trans & Infrastructure'],
                ['BC725',0,'Min of Trans & Infrastructure'],
                ['BC002',394,'Ministry of Education'],
                ['BC062',0,'Ministry of Education'],
                ['BC022',1446,'Ministry of Finance'],
                ['BC068',0,'Ministry of Finance'],
                ['GCPE',260,'GCPE'],
                ['BC127',368,'Ministry of Labour'],
                ['BC060',394,'Municipal Affairs'],
                ['BC106',6,'Off of the Merit Commissioner'],
                ['BC025',32,'Off of the Police Complaint Co'],
                ['BC009',43,'Office of Info & Priv Comm'],
                ['BC003',114,'Office of the Auditor General'],
                ['BC007',93,'Office of the Ombudsperson'],
                ['BC004',91,'Office of the Premier'],
                ['BC010',3775,'Public Safety & Sol General'],
                ['EMBC',259,'EMBC'],
                ['BC077',138,'Royal BC Museum'],
                ['BC031',1990,'Social Dev & Poverty Reduction'],
                ['BC126',73,'Tourism, Arts, Culture & Sport'],
                ['BC312',0,'InBC Investment Corp'],
        ];


        $as_of_date = '2023-09-01';

        ElligibleEmployee::where('year', 2023)->where('as_of_date', $as_of_date)->delete();
        EligibleEmployeeDetail::where('year', 2023)->where('as_of_date', $as_of_date)->delete();

        foreach($data_2021 as $data) {

            $bu = BusinessUnit::where('code', $data[0])->first();
            
            if (!$bu) {
                print 'Business Unit ' . $data[0] . ' not found ' . PHP_EOL;    
            }
             
            ElligibleEmployee::create([
                'as_of_date' => $as_of_date,
                'ee_count' => $data[1],
                'business_unit' => $data[0],
                'business_unit_name' => $bu  ? $bu->name : $data[2],
                'cde' => 'GOV',
                'year' => '2023',
            ]);

        }

    }
}
