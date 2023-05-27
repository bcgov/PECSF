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
                    'answer' => 'Contributions are disbursed twice a year to the various charities chosen by donors. In August, for deductions/donations from January to June, and in March, for deductions/donations from July to December.'
                ],
                [
                    'question' => 'How much of my donation will my charity receive?',
                    'answer' => 'The BC Public Service covers all administrative costs to run the PECSF program, as a result, 100% of employee donated dollars go to the charities you choose.'
                ],
                [
                    'question' => 'When will I receive a charitable tax receipt?',
                    'answer' => 'Payroll deductions automatically show on your T4 issued from payroll.  If you have donated via cash/cheque, your charitable donation receipt will be mailed to you the following spring.'
                ],
                [
                    'question' => 'What is the fund supported pool program? ',
                    'answer' => 'This refers to a community-based, charitable organization, chapter or program that has applied for, and been approved to receive a portion of funding, from a regional pool. These organizations are selected by your local PECSF regional committee and must report annually on the status of their approved program to continue to receive funding during each 3-year funding cycle. '
                ]
            ],
            'Volunteering' => [
                [
                    'question' => 'Where do I find more information on volunteering with the PECSF Annual Campaign?  ',
                    'answer' => 'You will find volunteering information in the  <a href="/volunteering">Volunteer Section</a> which includes contact information for lead coordinators, possible roles, training, resources and blogs.'
                ],
                [
                    'question' => 'How much time on average do I have to commit to as a volunteer?',
                    'answer' => 'Average time commitment can vary by role. Average time commitment for Coordinators is 12 to 16 hours (8 hours pre-campaign, 2 hours during, 2 hours after), plus PECSF training online. The time needed to successfully host an event varies considerably. Consider the costs and projected benefits of your event before you get started. '
                ],
                [
                    'question' => 'Does volunteering with PECSF count towards professional development?',
                    'answer' => 'Absolutely! You and your manager can decide how best to use your PECSF volunteer role to demonstrate skills, knowledge, and abilities to benefit yourself, your team, and the BC Public Service. '
                ]
            ],
            'Canlendar' => [
                [
                    'question' => 'How do charities get supported through PECSF?',
                    'answer' => 'By employees choosing them! Any charity, registered and in good standing CRA (Canada Revenue Agency), is eligible for funding through PECSF. Additionally, PECSF offers charities the opportunity to apply for specific local program funding every three years through the Fund Supported Pool initiative. '
                ],
                [
                    'question' => 'Who are the PECSF Headquarters (team)? ',
                    'answer' => 'The PECSF program is coordinated by a team within the BC Public Service Agency. This team is responsible for volunteer recruitment, training, recognition, and all PECSF program operational support provincially.'
                ],
                [
                    'question' => 'Who are the PECSF Board of Directors?',
                    'answer' => 'PECSF is registered federally, with the CRA (Canada Revenue Agency) as a Charitable Employee Trust. It is governed by a Board of Directors made up of current BC Public Servants. '
                ]
            ]
        ];
        return view('contact.index', compact('sections'));
    }
}
