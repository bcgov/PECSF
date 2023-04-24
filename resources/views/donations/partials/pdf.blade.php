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
</style>

<div class="header">
    <img src="img/brand/1.png"/>
    <span>PECSF Donation Summary</span>
    <div class="clear"></div>
</div>
<hr>

<span><i>Please note that this is not a Tax Receipt</i></span>
<div id="accordion">

    @foreach($pledges_by_yearcd as $key => $pledges)
        <div class="card">
            <div class="card-header" id="heading0{{ $loop->index }}">
                <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse0{{ $loop->index }}"
                    aria-expanded="{{ $loop->index == 0 ? 'true' : 'false' }}" aria-controls="collapse">
                    <h1 class="">
                        {{  $key }}
                    </h1>
                    <div class="flex-fill"></div>
                    <div class="expander">
                    </div>
                </h5>
            </div>

            <div id="collapse0{{ $loop->index }}" class="collapse {{ $loop->index == 0 ? 'show' : '' }}" aria-labelledby="heading0{{ $loop->index }}" data-parent="#accordion">
                <div class="card-body">
                    <table class="table  rounded">
                        <tr class="bg-light">
                            <th>Donation Type</th>
                            <th>Benefitting Charity</th>
                            <th>Frequency</th>
                            <th>Amount</th>

                        </tr>
                        @php $total = 0; @endphp
                        @foreach($pledges as $pledge)
                            <tr class="">
                                <td>{{ $pledge->donation_type }}</td>
                                @if ($pledge->type == 'P')
                                    <td>{{ $pledge->region }}
                                    </td>
                                @else
                                    <td>
                                        @foreach($pledge->distinct_charities()->get() as $charity)
                                        {{ $charity->name }}<br>
                                        @endforeach
                                    </td>
                                @endif
                                <td>{{ $pledge->frequency }} </td>

                                <td class="text-right">$ {{ number_format($pledge->pledge,2) }} </td>
                                {{-- <td class="text-right">$ {{ $pledge->frequency == 'Bi-Weekly' ?
                                        number_format($pledge->pay_period_amount * $pledge->campaign_year->number_of_periods,2) :
                                        number_format($pledge->one_time_amount,2) }} </td> --}}

                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

    @endforeach

</div>
