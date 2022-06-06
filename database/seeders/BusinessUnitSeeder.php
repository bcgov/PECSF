<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        //
        $business_units = [
            ['code' => 'BC115', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Env Assessment Office'],
            ['code' => 'BC058', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Natural Gas Development'],
            ['code' => 'BC065', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'International Trade'],
            ['code' => 'BCGOV', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'B.C. Government'],
            ['code' => 'BC000', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Government of B.C.'],
            ['code' => 'BC002', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Legislative Assembly'],
            ['code' => 'BC003', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Office of the Auditor General'],
            ['code' => 'BC004', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Office of the Premier'],
            ['code' => 'BC005', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Conflict of Interest Commiss\'r'],
            ['code' => 'BC006', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Agriculture, Food & Fisheries'],
            ['code' => 'BC007', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Office of the Ombudsperson'],
            ['code' => 'BC009', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Office of Info & Priv Comm'],
            ['code' => 'BC010', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Public Safety & Sol General'],
            ['code' => 'BC011', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Off of the Child & Youth Advoc'],
            ['code' => 'BC015', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Elections BC'],
            ['code' => 'BC019', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Adv Ed, Skills & Training'],
            ['code' => 'BC020', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Min of Tech, Trade & Eco Dev '],
            ['code' => 'BC022', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Ministry of Finance'],
            ['code' => 'BC023', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Info., Science & Tech. Agency'],
            ['code' => 'BC024', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Env Assmt/Land Use Coord Off'],
            ['code' => 'BC025', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Off of the Police Complaint Co'],
            ['code' => 'BC026', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Health'],
            ['code' => 'BC027', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Forensic Psychiatric'],
            ['code' => 'BC031', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Social Dev & Poverty Reduction'],
            ['code' => 'BC034', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Min of Trans & Infrastructure'],
            ['code' => 'BC035', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Fisheries'],
            ['code' => 'BC039', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Children & Family Development'],
            ['code' => 'BC044', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Skills Development & Labour'],
            ['code' => 'BC047', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Intergovernmental Relations'],
            ['code' => 'BC048', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Env & Climate Change Strategy'],
            ['code' => 'BC050', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Forests and Lands'],
            ['code' => 'BC055', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'BC Utilities Commission'],
            ['code' => 'BC057', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Energy, Mines & Low Carb Inn.'],
            ['code' => 'BC060', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Municipal Affairs'],
            ['code' => 'BC062', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Education and Child Care'],
            ['code' => 'BC067', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Product Services'],
            ['code' => 'BC068', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Public Sector Employer Council'],
            ['code' => 'BC071', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Public Service Empl Relat Comm'],
            ['code' => 'BC077', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Royal BC Museum'],
            ['code' => 'BC079', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Forest Practices Board'],
            ['code' => 'BC080', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Environm\'l Bds & Forest Comm\'s'],
            ['code' => 'BC085', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'BC Family Bonus'],
            ['code' => 'BC088', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'BC Pension Corp'],
            ['code' => 'BC092', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'BC Investment Management Corp'],
            ['code' => 'BC093', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Provincial Capital Commission'],
            ['code' => 'BC094', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Forest Renewal BC'],
            ['code' => 'BC095', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Broadmead Care Society'],
            ['code' => 'BC096', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'OBL Continuing Care Society'],
            ['code' => 'BC098', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Land and Water BC Inc'],
            ['code' => 'BC099', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Partnership BC Inc'],
            ['code' => 'BC0CT', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Corporate Training'],
            ['code' => 'BC105', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Attorney General'],
            ['code' => 'BC110', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Health Planning'],
            ['code' => 'BC112', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Citizens\' Services'],
            ['code' => 'BC130', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Agriculture and Food'],
            ['code' => 'BC160', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Small Business & Revenue'],
            ['code' => 'BC108', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Citizen\'s Assembly'],
            ['code' => 'BC801', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Freshwater Fisheries Society'],
            ['code' => 'BC802', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'BC Safety Authority'],
            ['code' => 'BC803', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Business Prac & Cons Prot Auth'],
            ['code' => 'BC804', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Partnerships British Columbia'],
            ['code' => 'TSSBC', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Telus Employer Solutions Inc.'],
            ['code' => 'BC805', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Community Living BC'],
            ['code' => 'BC100', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'BC Public Service Agency'],
            ['code' => 'BC120', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Indigenous Relations & Recon'],
            ['code' => 'BC125', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Jobs, Economic Recovery & Inn'],
            ['code' => 'CANBC', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Regulatory Region'],
            ['code' => 'BC106', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Off of the Merit Commissioner'],
            ['code' => 'SHARE', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Share for Recruit'],
            ['code' => 'BC109', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'BC Rep for Children & Youth'],
            ['code' => 'BC826', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Emerg & Health Services Comm'],
            ['code' => 'BC122', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Min of Healthy Living & Sport'],
            ['code' => 'BC806', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Pacific Carbon Trust Inc.'],
            ['code' => 'BC127', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Ministry of Labour'],
            ['code' => 'BC128', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Ministry of Forests'],
            ['code' => 'BC129', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Min of Science & Universities'],
            ['code' => 'BC131', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Ministry of Housing'],
            ['code' => 'BC104', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Public Guardian and Trustee'],
            ['code' => 'BC063', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Teachers Act Special Account'],
            ['code' => 'BC132', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => 'Sm Bus and Red Tape Reduction'],
            ['code' => 'BC126', 'status' => 'A', 'effdt' => '2017-09-21', 'name' => 'Tourism, Arts, Culture & Sport'],
            ['code' => 'BC029', 'status' => 'A', 'effdt' => '2018-09-05', 'name' => 'Mental Health & Addictions'],
            ['code' => 'BC825', 'status' => 'A', 'effdt' => '2015-01-01', 'name' => 'Destination BC Corp.'],
            ['code' => 'BC601', 'status' => 'A', 'effdt' => '2019-10-27', 'name' => 'BCFinancial Services Authority'],
            ['code' => 'BC0XD', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XF', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XT', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XK', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC070', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XB', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC083', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC056', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC008', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC028', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC040', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC082', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XG', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XM', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XC', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XY', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC090', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XA', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XV', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC018', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XW', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XU', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC072', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XJ', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XH', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XE', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC053', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC064', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC045', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XX', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XZ', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC0XR', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            ['code' => 'BC036', 'status' => 'I', 'effdt' => '2015-01-01', 'name' => ''],
            
        ];

        DB::table('business_units')->truncate();

        foreach ($business_units as $business_unit) {
            \App\Models\BusinessUnit::updateOrCreate([
                'code' => $business_unit['code'],
            ], [
                'effdt' => $business_unit['effdt'],
                'name' => $business_unit['name'],
                'status' => $business_unit['status'],
                'created_by_id' => 1,
                'updated_by_id' => 1,
            ]);

        }



    }
}
