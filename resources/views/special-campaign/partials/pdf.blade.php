<style>
    #accordion{

    }
    .header{
        padding:25px;
    }

    .header img{
        left:left;
        width:350px;
        height:auto;
    }

    .header span{
        float:right;
        font-weight:bold;
        display:block;
        vertical-align: bottom;
        font-size:20px;
        position:relative;
        bottom:-55px;
    }

    table {
        border-collapse: collapse;
        padding-left: 1em;
        padding-right: 1em;
    }

    table.summary td { 
        padding-top: 1em;
    }
    table.summary tr td:nth-of-type(1) {
        width: 35%;
        font-weight: bold;            
    }
    table.summary td td:nth-of-type(2) {
        width: 55%;
    }
    .bg-light, .bg-light > a {
        color: #1f2d3d !important;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .float-right {
        float: right;
    }

</style>

<div class="header">
    <img  src="img/brand/1.png"/>
    <img class="pdf-logo-image" style="float:right;width:150px;height:auto;" src="img/brand/PECSF_Logo_Vert_RGB.jpg"/><br>
    <div class="clear"></div>
</div>
<br>
<hr>
<h4 style="text-align:center;width:100%;">Donation - Special Campaign</h4>


<span><i>Please note that this is not a tax receipt. Payroll deductions will appear on your payroll issued T4 for year when the funds are collected.</i></span>

<div class="container">

    <div class="row">
        <div class="col-12 col-sm-7">
            <h5 class="mt-3">Name: {{ $user->name }}</h5>
            <h5>Date : {{ date("F d, Y") }}</h5>
                
            <div class="card p-3">
                <h3 class="card-title">Deductions</h3>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <span><b>Your One-time payroll deductions: </b></span>
                        {{-- <span class="float-right">${{ $calculatedTotalAmountOneTime }}</span> --}}
                        <span class="float-left">${{ number_format($one_time_amount ,2) }}</span>
                        <hr>

                    </div>
                </div>
            </div>    
        </div>
                
        <h3>One-Time donation disbursement</h3>
        <hr>

        <div class="card mt-3">
            <div class="card-body">
                <table class="summary">
                    <tr>
                        <td>In support of :</td>
                        <td>{{ $in_support_of }}</td>
                    </tr>
            
                    <tr class="row">
                        <td class="col-6">Initative :</td>
                        <td class="col-6">{{ $special_campaign_name }}</td>
                    </tr>

                    <tr class="row">
                        <td class="col-6">Campaign period :</td>
                        <td class="col-6">From {{ $special_campaign->start_date->format('M j, Y') }} to {{ $special_campaign->end_date->format('M j, Y') }}</td>
                    </tr>
                    
                    <tr>
                        <td class="col-6">Deduction date :</td>
                        <td class="col-6"> {{ $check_dt->format('Y-m-d') }}</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

</div>
