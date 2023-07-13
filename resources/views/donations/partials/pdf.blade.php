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
        border-collapse: collapse;
    }

    table th{
        font-weight:bold;
        background:#f7f7f7;
        color:#000;
        font-size:14px;
        padding:10px;
        text-align:left;

    }

    table td{
        text-align:left;
        font-weight:normal;
        padding:10px;
    }

    table tr{
        border-bottom:#ccc 1px solid;
    }
</style>

<div class="header">
    <img  src="img/brand/1.png"/>
    <img style="height:80px;float:right;width:130px;" src="img/brand/5.png"/><br>

    <div class="clear"></div>
</div>
<br>
<hr>
<h4 style="text-align:center;width:100%;">PECSF Donation History Summary</h4>

<h5>Date @php
    echo date("d-m-Y",time());
@endphp</h5>
<span><i>Please note that this is not a Tax Receipt</i></span>
<div id="accordion">

    @foreach($pledges_by_yearcd as $key => $pledges)
        <div class="card">
            <div class="card-header" id="heading0{{ $loop->index }}">
                <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse0{{ $loop->index }}"
                    aria-expanded="{{ $loop->index == 0 ? 'true' : 'false' }}" aria-controls="collapse">
                    <h4 class="">
                        Campaign Year {{  $key }}
                    </h4>
                    <div class="flex-fill"></div>
                    <div class="expander">
                    </div>
                </h5>
            </div>

            <div id="collapse0{{ $loop->index }}" class="collapse {{ $loop->index == 0 ? 'show' : '' }}" aria-labelledby="heading0{{ $loop->index }}" data-parent="#accordion">
                <div class="card-body">
                    <table class="table  rounded">
                        <tr class="bg-light">
                            <th style="width:18%;">Donation Type</th>
                            <th style="width:62%;">Benefitting Charity</th>
                            <th style="width:10%;">Frequency</th>
                            <th style="width:10%;">Amount</th>

                        </tr>
                        @php $total = 0; @endphp
                        @foreach($pledges as $pledge)
                            <tr class="">
                                <td>{{ $pledge->donation_type }}</td>
                                @if ($pledge->type == 'P')
                                    {{-- <td>{{ $pledge->fund_supported_pool->region->name ?? '' }}  --}}
                                    <td>{{ $pledge->region }}   </td>
                                @else
                                    <td style="text-overflow: ellipsis;">
                                        @if ($pledge->source == 'GF')
                                            @switch($pledge->donation_type)
                                                @case('Special Campaign')
                                                    {{ $pledge->region }}
                                                    @break
                                                @case('Donate Now')
                                                    {{ $pledge->region }}
                                                    @break
                                                @default
                                                    @foreach(explode(",",$pledge->number_of_charities) as $charity)
                                                        <a  style="cursor:pointer;text-overflow: ellipsis;" class="more-info"
                                                            data-source="{{ $pledge->source }}"
                                                            data-type="{{ $pledge->donation_type }}"
                                                            data-id="{{ $pledge->id }}"
                                                            data-frequency="{{ $pledge->frequency }}"
                                                            data-yearcd="{{ $pledge->yearcd }}">
                                                            {{$charity}}
                                                        </a>
                                                        <br>
                                                    @endforeach

                                            @endswitch
                                        @else
                                            @if ($pledge->donation_type == 'Donate Today')
                                                {{ $pledge->number_of_charities }}
                                            @else
                                                @foreach(explode(",",$pledge->number_of_charities) as $charity)
                                                    <a  style="cursor:pointer;text-overflow: ellipsis;" class="more-info"
                                                        data-source="{{ $pledge->source }}"
                                                        data-type="{{ $pledge->donation_type }}"
                                                        data-id="{{ $pledge->id }}"
                                                        data-frequency="{{ $pledge->frequency }}"
                                                        data-yearcd="{{ $pledge->yearcd }}">
                                                        {{$charity}}
                                                    </a>
                                                    <br>
                                                @endforeach
                                            @endif
                                        @endif
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
