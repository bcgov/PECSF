<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactFaqController extends Controller
{
    public function index() {
        $sections = [
            'Donations' => [
                [
                    'question' => 'When will my charity receive my donation?',
                    'answer' => 'Contributions are disbursed twice a year to the various charities chosen by donors. In August for deductions/donations from January to June, and in March for deductions/donations from July to December.'
                ],
                [
                    'question' => 'How much of my donation will my charity receive?',
                    'answer' => 'The BC Public Service covers all administrative costs to run the PECSF program, as a result, 100% of employee donated dollars go to the charities you choose.'
                ],
                [
                    'question' => 'When will I receive a charitable tax receipt?',
                    'answer' => 'Payroll deductions automatically show on your T4 issued from payroll.  If you have donated via cash/cheque, your charitable donation receipt will be mailed to you the following Spring.'
                ]
            ],
            'Volunteering' => [
                [
                    'question' => 'How and where do I register to be a volunteer?',
                    'answer' => 'To begin the Volunteer Registration process, please click <a href="/volunteering">here</a>'
                ],
                [
                    'question' => 'How much time on average do I have to commit to as a volunteer?',
                    'answer' => 'Average time commitment can vary my role.  Average time commitment for Coordinators is 12 to 16 hours (8 hours’ pre-campaign, 2 hours during, 2 hours after) plus PECSF training online.  The time needed to successfully host an event varies considerably.  Consider the costs and projected benefits of your event before you get started.'
                ],
                [
                    'question' => 'Does volunteering with PECSF count towards professional development?',
                    'answer' => 'Absolutely!  You and your manager have the ability to decide how best to use your PECSF volunteer role to demonstrate skills, knowledge and ability that benefit both yourself, your team, and the BC Public Service overall.'
                ]
            ],
            'Calendar' => [
                [
                    'question' => 'How do charities get supported through PECSF?',
                    'answer' => 'By employees choosing them!  Any charity registered with the CRA and in good standing is eligible for funding through PECSF.  Additionally, PECSF offers charities the chance to apply for focus local program funding every three years.'
                ],
                [
                    'question' => 'Who are PECSF Headquarters?',
                    'answer' => 'The PECSF program is coordinated through a central team within the BC Public Service Agency.  This team is responsible for recruitment, volunteer training, recognition, and all operational support for the PECSF giving and volunteer program provincially.'
                ],
                [
                    'question' => 'Who are the PECSF Board of Directors?',
                    'answer' => 'PECSF is registered federally as a Charitable Employee Trust.  It is governed by a board of directors made up of current BC Public Servants.'
                ]
            ]
        ];
        return view('contact.index', compact('sections'));
    }
}
