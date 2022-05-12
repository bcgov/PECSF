<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $organizations = [
        //     'BC Pension Corporation',
        //     'BC Securities Commission',
        //     'Community Living BC',
        //     'Destination BC Corporation',
        //     'Elections BC',
        //     'Emergency Management BC',
        //     'Environmental Assessment Office',
        //     'Forest Practices Board',
        //     'Government Communications & Public Engagement',
        //     'Legislative Assembly',
        //     'Liquor Distribution Branch',
        //     'Ministry of Advanced Education, Skills & Training',
        //     'Ministry of Agriculture',
        //     'Ministry of Attorney General',
        //     'Ministry of Children & Family Development',
        //     'Ministry of Citizens\' Services',
        //     'Ministry of Education',
        //     'Ministry of Energy, Mines & Petroleum Resources',
        //     'Ministry of Environment & Climate Change Strategy',
        //     'Ministry of Finance',
        //     'Ministry of Forests, Lands, Natural Resource Operations & Rural Development',
        //     'Ministry of Health',
        //     'Ministry of Indigenous Relations & Reconciliation',
        //     'Ministry of Jobs, Economic Development & Competitiveness',
        //     'Ministry of Labour',
        //     'Ministry of Mental Health & Addictions',
        //     'Ministry of Municipal Affairs & Housing',
        //     'Ministry of Public Safety & Solicitor General',
        //     'Ministry of Social Development & Poverty Reduction',
        //     'Ministry of Tourism, Arts & Culture',
        //     'Ministry of Transportation & Infrastructure',
        //     'Office of Information & Privacy Commissioner',
        //     'Office of the Auditor General of BC',
        //     'Office of the Merit Commissioner',
        //     'Office of the Ombudsperson',
        //     'Office of the Premier',
        //     'Public Service Agency',
        //     'Representative for Children & Youth',
        //     'Royal BC Museum',
        //     'Other (Includes Retirees)'
        // ];

        $organizations = [
            ['code' => 'FSA', 'effdt' => '2019-10-27', 'name' => 'BCFSA', 'status' => 'A'],
            ['code' => 'GOV', 'effdt' => '2004-01-01', 'name' => 'Government of BC', 'status' => 'A'],
            ['code' => 'BCA', 'effdt' => '2015-07-30', 'name' => 'BC Ambulance', 'status' => 'I'],
            ['code' => 'LA',  'effdt' => '2004-01-01', 'name' => 'Legislative Assembly', 'status' => 'A'],
            ['code' => 'FP',  'effdt' => '2015-07-30', 'name' => 'Forensic Psychiatric', 'status' => 'I'],
            ['code' => 'LDB', 'effdt' => '2004-01-01', 'name' => 'Liquor Distribution Branch', 'status' => 'A'],
            ['code' => 'BCS', 'effdt' => '2004-01-01', 'name' => 'BC Securities', 'status' => 'A'],
            ['code' => 'PAR', 'effdt' => '2006-03-01', 'name' => 'Partnerships BC', 'status' => 'I'],
            ['code' => 'TSS', 'effdt' => '2006-03-01', 'name' => 'Telus Sourcing Solutions', 'status' => 'I'],
            ['code' => 'HLN', 'effdt' => '2009-01-01', 'name' => 'Health Link Nurses', 'status' => 'A'],
            ['code' => 'RET', 'effdt' => '2010-09-15', 'name' => 'Retirees','status' => 'A'],
        ];

        foreach ($organizations as $organization) {
            Organization::updateOrCreate([
                'code' => $organization['code'],
            ],[
                'name' => $organization['name'],
                'effdt' => $organization['effdt'],
                'status' => $organization['status'],
            ]);
        }
    }
}
