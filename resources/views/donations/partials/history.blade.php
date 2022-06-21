<div id="accordion">
    
    @foreach($old_pledges_by_yearcd as $key => $pledges)
    <div class="card">
        <div class="card-header" id="heading0{{ $loop->index }}">
            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse0{{ $loop->index }}" 
                   aria-expanded="{{ $loop->index == 0 ? 'true' : 'false' }}" aria-controls="collapse">
                <button class="btn btn-link font-weight-bold">
                    {{  $key }}
                </button>
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
                        <th></th>
                    </tr>
                    @php $total = 0; @endphp
                    @foreach($pledges as $pledge)
                        <tr class="">
                            <td>{{ $pledge->donation_type }}</td>
                            @if ($pledge->type == 'P') 
                                <td>{{ $pledge->fund_supported_pool->region->name ?? '' }} 
                                </td>
                            @else
                                <td>{{ '' }} </td>
                            @endif
                            <td>{{ $pledge->frequency }} </td>
                            <td class="text-right">$ {{ $pledge->frequency == 'Bi-Weekly' ?  
                                        number_format($pledge->pay_period_amount * $pledge->campaign_year->number_of_periods,2) : 
                                        number_format($pledge->one_time_amount,2) }} </td>
                            <td class="text-right">
                                {{-- @if ($pledge->campaign_type == 'Annual')  --}}
                                <button type="button" class="more-info btn btn-sm btn-outline-primary" 
                                            data-source="{{ "local" }}"
                                            data-type="{{ $pledge->donation_type }}"
                                            data-id="{{ $pledge->id }}"
                                            data-frequency="{{ $pledge->frequency }}"
                                            data-yearcd="{{ $pledge->campaign_year->calendar_year }}">Details
                                </button>
                                {{-- @endif --}}
                            </td>                                        
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    
    @endforeach

    @foreach($old_bi_pledges_by_yearcd as $key => $pledges)

    <div class="card">
        <div class="card-header" id="heading1{{ $loop->index }}">
            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse1{{ $loop->index }}" 
                   aria-expanded="{{ (count($old_pledges_by_yearcd) == 0 and $loop->index == 0) ? 'true' : 'false' }}" aria-controls="collapse">
                <button class="btn btn-link font-weight-bold">
                    {{  $key }}
                </button>
                <div class="flex-fill"></div>
                <div class="expander">
                    
                </div>
            </h5>
        </div>

        <div id="collapse1{{ $loop->index }}" class="collapse {{ (count($old_pledges_by_yearcd) == 0 and $loop->index == 0) ? 'show' : '' }}" aria-labelledby="heading1{{ $loop->index }}" data-parent="#accordion">
            <div class="card-body">
                <table class="table  rounded">
                    <tr class="bg-light">
                        <th>Donation Type</th>
                        <th>Benefitting Charity</th>
                        <th>Frequency</th>
                        {{-- <th>Pledge</th> --}}
                        <th>Amount</th>
                        <th></th>
                    </tr>
                    @php $total = 0; @endphp
                    @foreach($pledges as $pledge)
                        <tr class="">
                            <td>{{$pledge->campaign_type }}</td>
                            {{-- <td class="text-left">{{ $pledge->source == 'Pool' ? $pledge->region->name : $pledge->charity->charity_name }} </td> --}}
                            <td >
                                @if ($pledge->source == 'Pool')
                                     <div>{{ $pledge->name1 ?? '' }}</div> 
                                    <div>{{ $pledge->name2 ?? '' }}</div> 
                                @endif
                            </td>
                            <td>{{$pledge->frequency}} </td>
                            <td class="text-right">${{ 
                                $pledge->frequency == 'Bi-Weekly' ? 
                                    number_format($pledge->pledge * $pledge->campaign_year->number_of_periods,2) :                        
                                    number_format($pledge->pledge,2) }} 
                            </td>
                            <td class="text-right">
                                @if ($pledge->campaign_type == 'Annual') 
                                    <button type="button" class="more-info btn btn-sm btn-outline-primary" 
                                            data-source="{{ "history" }}"
                                            data-frequency="{{ $pledge->frequency }}"
                                            data-yearcd="{{ $pledge->yearcd }}">Details</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        {{-- <td colspan="4" class="text-center"><strong>  In {{$pledges[0]->created_at->format('Y')}}, you pledged ${{$total}}</strong></td> --}}
                    </tr>
                </table>
            </div>
        </div>
    </div>

@endforeach

    {{-- <div class="card">
        <div class="card-header" id="heading">
            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse" aria-expanded="true" aria-controls="collapse">
                <button class="btn btn-link font-weight-bold">
                    {{$currentYear}}
                </button>
                <div class="flex-fill"></div>
                <div class="expander">
                </div>
            </h5>
        </div>
        <div id="collapse" class="collapse show" aria-labelledby="heading" data-parent="#accordion">
            <div class="card-body">
                <table class="table table-bordered rounded">
                    <tr class="text-center bg-light">
                        <th>Organization Name</th>
                        <th>Amount</th>
                        <th>Donation Type</th>
                        <th>Frequency</th>
                    </tr>
                    @php $total = 0; @endphp
                    @foreach($pledges as $pledge)
                        @foreach($pledge->charities as $charity)
                        @php $total += $charity->goal_amount; @endphp
                        <tr class="text-center">
                            <td class="text-left">{{$charity->charity->charity_name}} </td>
                            <td class="text-left">{{$charity->goal_amount}} </td>
                            <!-- <td>{{$pledge->created_at->format('F j, Y')}}</td> -->
                            @if ($pledge->campaign_year)
                                <td>{{$pledge->campaign_year->calendar_year}} Campaign</td>
                            @endif
                            <td>{{$charity->frequency == 'bi-weekly' ? 'Bi-weekly' : 'One-time'}}</td>
                        </tr>
                        @endforeach
                    @endforeach

                    <tr>
                        <td colspan="4" class="text-center"><strong>  In {{$pledges[0]->created_at->format('Y')}}, you pledged ${{$total}}</strong></td>
                    </tr>
                </table>
                <div class="text-center mt-3">
                    <div class="row">
                        <div class="col-6 px-5 offset-3">
                            <button class="btn btn-block btn-outline-primary">Export Summary</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="heading2">
            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse">
                <button class="btn btn-link font-weight-bold">
                    2021
                </button>
                <div class="flex-fill"></div>
                <div class="expander">
                    
                </div>
            </h5>
        </div>

        <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordion">
            <div class="card-body">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="heading3">
            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse">
                <button class="btn btn-link font-weight-bold">
                    2020
                </button>
                <div class="flex-fill"></div>
                <div class="expander">
                    
                </div>
            </h5>
        </div>

        <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion">
            <div class="card-body">
            </div>
        </div>
    </div> --}}
</div>