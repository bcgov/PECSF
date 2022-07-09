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

    table td{
        text-align:center;
        font-weight:normal;
        padding:10px;
    }

    *{
        font-family: "BCSans", "Noto Sans", Verdana, Arial, sans-serif;
    }
</style>

<div class="header">
    <img src="img/brand/1.png"/>
    <span>PECSF Donation Summary</span>
    <div class="clear"></div>
</div>
<hr>

<span>Please note that this is not a tax receipt. Payroll deductions begin with the first paycheque in January and will appear on your payroll issued T4 for year when the funds are collected. PECSF issues chequeus twice a year. In August for payroll deductions from Janurary-June, and in March for payroll deductions from July - December</span>

<div class="container">
    <form action="{{route('donate.save.summary')}}" method="POST">
        <div class="row">
            <div class="col-12 col-sm-7">
                <h2 class="mt-5">{{ $date }}</h2>
                <div class="card bg-light p-3">
                    <h1 class="card-title">Deductions</h1>
                    <hr>
                    <div class="card">
                        <div class="card-body">
                            <span><b>Your Bi-weekly payroll deductions:</b></span>
                            {{-- <span class="float-right mb-2">${{ $calculatedTotalAmountBiWeekly*26 }}</span><br> --}}
                            <span class="float-right mb-2">${{ number_format($annualBiWeeklyAmount,2) }}</span><br>
                            <h6>AND / OR</h6>
                            <span><b>Your One-time payroll deductions:</b></span>
                            {{-- <span class="float-right">${{ $calculatedTotalAmountOneTime }}</span> --}}
                            <span class="float-right">${{ number_format($annualOneTimeAmount,2) }}</span>
                            <hr>
                            <p class="text-right"><b>Total :</b> ${{ number_format($annualBiWeeklyAmount + $annualOneTimeAmount,2) }}</p>
                        </div>
                    </div>
                    @csrf
                    @foreach(['one-time', 'bi-weekly'] as $key)
                        @if($key === 'one-time' && (session()->get('amount-step')['frequency'] === 'one-time' || session()->get('amount-step')['frequency'] === 'both'))
                            @php $key_ = $key; @endphp
                            @php $keyCase = 'oneTime'; @endphp
                            @php $multiplier = 1; @endphp
                            @include('donate.partials.summary-distribution-pdf')
                        @endif
                        @if($key === 'bi-weekly' && (session()->get('amount-step')['frequency'] === 'bi-weekly' || session()->get('amount-step')['frequency'] === 'both'))
                            @php $key_ = $key;@endphp
                            @php $keyCase = 'biWeekly'; @endphp
                            @php $multiplier = 26; @endphp
                            @include('donate.partials.summary-distribution-pdf')
                        @endif
                    @endforeach
                </div>

                <div class="">
                    @if ($pool_option == 'C')
                        @foreach ($charities as $charity)
                            <input type="hidden" name="charityOneTimeAmount[{{$charity['id']}}]" value="{{$charity['one-time-amount-distribution']}}">
                            <input type="hidden" name="charityBiWeeklyAmount[{{$charity['id']}}]" value="{{$charity['bi-weekly-amount-distribution']}}">
                            <input type="hidden" name="charityOneTimePercentage[{{$charity['id']}}]" value="{{$charity['one-time-percentage-distribution']}}">
                            <input type="hidden" name="charityBiWeeklyPercentage[{{$charity['id']}}]" value="{{$charity['bi-weekly-percentage-distribution']}}">
                            <input type="hidden" name="charityAdditional[{{$charity['id']}}]" value="{{$charity['additional']}}">
                            <input type="hidden" name="annualOneTimeAmount" value="{{$annualOneTimeAmount}}">
                            <input type="hidden" name="annualBiWeeklyAmount" value="{{$annualBiWeeklyAmount}}">
                            <input type="hidden" name="frequency" value="{{$frequency}}">
                            <input type="hidden" name="pool_option" value="{{$pool_option}}">
                            <input type="hidden" name="regional_pool_id" value="{{$regional_pool_id}}">
                        @endforeach
                    @else
                        <input type="hidden" name="annualOneTimeAmount" value="{{$annualOneTimeAmount}}">
                        <input type="hidden" name="annualBiWeeklyAmount" value="{{$annualBiWeeklyAmount}}">
                        <input type="hidden" name="frequency" value="{{$frequency}}">
                        <input type="hidden" name="pool_option" value="{{$pool_option}}">
                        <input type="hidden" name="regional_pool_id" value="{{$regional_pool_id}}">
                    @endif
                </div>

            </div>

        </div>

    </form>

</div>
