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
        $organizations = [
            'BC Pension Corporation',
            'BC Securities Commission',
            'Community Living BC',
            'Destination BC Corporation',
            'Elections BC',
            'Emergency Management BC',
            'Environmental Assessment Office',
            'Forest Practices Board',
            'Government Communications & Public Engagement',
            'Legislative Assembly',
            'Liquor Distribution Branch',
            'Ministry of Advanced Education, Skills & Training',
            'Ministry of Agriculture',
            'Ministry of Attorney General',
            'Ministry of Children & Family Development',
            'Ministry of Citizens\' Services',
            'Ministry of Education',
            'Ministry of Energy, Mines & Petroleum Resources',
            'Ministry of Environment & Climate Change Strategy',
            'Ministry of Finance',
            'Ministry of Forests, Lands, Natural Resource Operations & Rural Development',
            'Ministry of Health',
            'Ministry of Indigenous Relations & Reconciliation',
            'Ministry of Jobs, Economic Development & Competitiveness',
            'Ministry of Labour',
            'Ministry of Mental Health & Addictions',
            'Ministry of Municipal Affairs & Housing',
            'Ministry of Public Safety & Solicitor General',
            'Ministry of Social Development & Poverty Reduction',
            'Ministry of Tourism, Arts & Culture',
            'Ministry of Transportation & Infrastructure',
            'Office of Information & Privacy Commissioner',
            'Office of the Auditor General of BC',
            'Office of the Merit Commissioner',
            'Office of the Ombudsperson',
            'Office of the Premier',
            'Public Service Agency',
            'Representative for Children & Youth',
            'Royal BC Museum',
            'Other (Includes Retirees)'
        ];
        foreach ($organizations as $organization) {
            Organization::updateOrCreate([
                'name' => $organization,
            ]);
        }
    }
}
