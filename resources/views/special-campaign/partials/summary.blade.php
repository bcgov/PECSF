<h3 class="mt-1">3. Summary</h3>
<p class="mt-3">Please review your donation plan and press <b>Pledge</b> when ready!</p>

<div class="card">
    <div class="card-body">
        
        <div class="container">
            <div class="row">
                <div class="col-6 font-weight-bold">Your One-time payroll deductions :</div>
                <div class="col-6">${{ number_format($one_time_amount,2) }}</div>
            </div>
        </div>

        <div class="container mt-2">
            <div class="row">
                <div class="col-6 font-weight-bold">In support of :</div>
                <div class="col-6">{{ $in_support_of }}</div>
            </div>
        </div>

        <div class="container mt-2">
            <div class="row">
                <div class="col-6 font-weight-bold">Initative :</div>
                <div class="col-6">{{ $special_campaign_name }}</div>
            </div>
        </div>

        <div class="container mt-2">
            <div class="row">
                <div class="col-6 font-weight-bold">Deduction date :</div>
                <div class="col-6">{{ $check_dt }}</div>
            </div>
        </div>

    </div>
</div>

<div class="row">
    <p class="py-4">
    Please note that <b>this is not a tax receipt</b>.
    Payroll deductions begin with the first paycheque in January and will appear on your payroll issued T4 for year when the funds are collected. 
    </p>
</div>


{{-- <div class="col-12 col-sm-5 text-center">
    <img src="{{ asset('img/donor.png') }}" alt="Group of volunteers making wreaths at a table" class="py-5 img-fluid">
        <p>
        Making changes to your pledge outside of Campaign time? Please contact <a href="mailto:PECSF@gov.bc.ca" class="text-primary">PECSF@gov.bc.ca</a> directly or submit an <a href="https://www2.gov.bc.ca/gov/content/careers-myhr" class="text-primary" target="_blank">AskMyHR</a> service request to make any changes on your existing pledge outside the annual campaign/open enrollment period (September-December).
    </p>
    <p><b>Questions?</b> <a href="https://www.gov.bc.ca/pecsf" class="text-primary" target="_blank">www.gov.bc.ca/pecsf</a>         <b>Email:</b> <a href="mailto:PECSF@gov.bc.ca" class="text-primary">PECSF@gov.bc.ca</a></p>
</div> --}}
    
<div>
    <strong>Freedom of Information and Protection of Privacy Act</strong>
    <p class="py-3">Personal information on this form is collected under sections 26 (c) and (e) of the Freedom of Information and Protection of Privacy Act by the BC Public Service Agency for the purposes of processing and reporting on your donation for charitable contributions to the Community Fund, fund disbursement and reconciliation, as well as program analysis and improvement purposes.</p>
    <p>By clicking the Pledge button, you hereby consent to the disclosure, within Canada only, by the BC Public Service Agency of your name to your organization’s Lead PECSF Coordinator for the purpose of administering the organization's participation incentive draws for the current campaign. This consent is effective until such time as my consent is revoked by you, in writing, to the PECSF Campaign Manager.</p>
    <p>Questions about the collection of your personal information can be directed to the Campaign Manager, Provincial Employees Community Services Fund, 
            at 250 356-1736, <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>.</p>
</div>


