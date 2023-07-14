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
        text-align:left;
    }

    table td:first-child {
        text-align:left;
    }

    table td{

        padding-left:8px;
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


</style>

<div class="header">
    <img  src="img/brand/1.png"/>
    <img style="height:80px;float:right;width:130px;" src="img/brand/5.png"/><br>
    <div class="clear"></div>
</div>
<br>
<hr>
<h4 style="text-align:center;width:100%;">PECSF Donate Now Summary</h4>


<span>Please note that this is not a tax receipt. Payroll deductions will appear on your payroll issued T4 for year when the funds are collected. PECSF issues cheques twice a year. In August, for payroll deductions from January - June, and in March, for payroll deductions from July - December.</span>


<div class="container">

    <div class="row">
        <div class="col-12 col-sm-7">
            <h5>Date @php
                    echo date("d-m-Y",time());
                @endphp</h5>
            <div class="card bg-light p-3">
                <h3 class="card-title">Deductions</h3>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <span><b>Your One-time payroll deductions:</b></span>
                        {{-- <span class="float-right">${{ $calculatedTotalAmountOneTime }}</span> --}}
                        <span class="float-right">${{ number_format($one_time_amount ,2) }}</span>
                        <hr>

                    </div>
                </div>

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
