<div id="accordion">
    @php
        $ignore = false;
    @endphp
    @foreach($pledges_by_yearcd as $key => $pledges)
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
                                {{-- <td>{{ $pledge->fund_supported_pool->region->name ?? '' }}  --}}
                                <td>{{ $pledge->region }}   </td>
                            @else
                                <td>{{ '' }} </td>
                            @endif
                            <td>{{ $pledge->frequency }} </td>
                            <td class="text-right">$ {{ number_format($pledge->pledge,2) }} </td>
                            <td class="text-right">
                                {{-- @if ($pledge->campaign_type == 'Annual')  --}}
                                <button type="button" class="more-info btn btn-sm btn-outline-primary"
                                            data-source="{{ $pledge->source  }}"
                                            data-type="{{ $pledge->donation_type }}"
                                            data-id="{{ $pledge->id }}"
                                            data-frequency="{{ $pledge->frequency }}"
                                            data-yearcd="{{ $pledge->yearcd }}">Details
                                </button>
                                {{-- @endif --}}
                            </td>
                        </tr>
                    @endforeach
                </table>

                @if($key > $currentYear || $ignore)
                    @php
                        $ignore = true;
                    @endphp
                @else

                <a style="margin-left: auto;
    margin-right: auto;
    width: fit-content;
    display: block;" href="/donate/duplicate/{{$pledge->id}}">
                <button type="button" class="pl-5 pr-5 duplicate align-content-center btn-lg btn-primary"
                        data-source="{{ $pledge->source  }}"
                        data-type="{{ $pledge->donation_type }}"
                        data-id="{{ $pledge->id }}"
                        data-frequency="{{ $pledge->frequency }}"
                        data-yearcd="{{ $pledge->yearcd }}">{{$ignore}}Duplicate this pledge
                </button>
                </a>
                @endif

            </div>
        </div>
    </div>

    @endforeach


</div>
