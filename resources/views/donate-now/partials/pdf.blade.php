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

    table{
        width:100%;
    }

    table th{
        font-weight:bold;
        background:#f7f7f7;
        color:#000;
        font-size:14px;
        padding:10px;
    }

    table td:first-child {
        text-align:left;
    }

    table td:last-child{
        text-align:right;
        font-weight:normal;
        padding:10px;
    }

    table td small {
        font-size:10px;
        font-weight:normal;
    }

    *{
        font-family: "BCSans", "Noto Sans", Verdana, Arial, sans-serif;
    }
</style>

<div class="header">
    <img src="img/brand/1.png"/>
    <img style="width:150px;float:right;" src="img/brand/4.bmp"/><br>
    <div style="float:right;font-weight:bold;font-size:18px;" >PECSF Donation Summary</div>
    <div class="clear"></div>
</div>
<br>
<hr>

<span>Please note that this is not a tax receipt. Payroll deductions begin with the first paycheque in January and will appear on your payroll issued T4 for year when the funds are collected. PECSF issues cheques twice a year. In August for payroll deductions from January - June, and in March for payroll deductions from July - December</span>

<div class="container">

    <div class="row">
        <div class="col-12 col-sm-7">
            <h2 class="mt-5">{{ $date }}</h2>
            <div class="card bg-light p-3">
                <h1 class="card-title">Deductions</h1>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <span><b>Your One-time payroll deductions:</b></span>
                        {{-- <span class="float-right">${{ $calculatedTotalAmountOneTime }}</span> --}}
                        <span class="float-right">${{ number_format($one_time_amount ,2) }}</span>
                        <hr>
                        <p class="text-right"><b>Total :</b> ${{ number_format($one_time_amount ,2) }}</p>
                    </div>
                </div>

                <p class="mt-4"><h1>Disbursement</h1></p>
                @foreach(['one-time'] as $key)
                    @php $viewMode = 'pdf'; @endphp
                    @if($key === 'one-time')
                        @php $key_ = $key; @endphp
                        @php $keyCase = 'oneTime'; @endphp
                        @php $multiplier = 1; @endphp
                        @include('annual-campaign.partials.summary-distribution')
                    @endif
                @endforeach
            </div>

        </div>
    </div>

</div>
