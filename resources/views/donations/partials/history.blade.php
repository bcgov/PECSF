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
                        <th class="text-right">Amount</th>
                        <th></th>
                    </tr>
                    @php $total = 0; $ignore = true; @endphp
                    @foreach($pledges as $pledge)
                        <tr class="">
                            <td style="width: 15%">{{ $pledge->donation_type }}</td>
                            @if ($pledge->type == 'P')
                                {{-- <td>{{ $pledge->fund_supported_pool->region->name ?? '' }}  --}}
                                <td>{{ $pledge->region }}   </td>
                            @else
                                <td>
                                @if ($pledge->source == 'GF')
                                    @switch($pledge->donation_type)
                                        @case('Special Campaign')
                                            {{ $pledge->region }} 
                                            @break
                                        @case('Donate Now')
                                            {{ $pledge->region }} 
                                            @break
                                        @default
                                        <a type="button" class="more-info"
                                            data-source="{{ $pledge->source }}"
                                            data-type="{{ $pledge->donation_type }}"
                                            data-id="{{ $pledge->id }}"
                                            data-frequency="{{ $pledge->frequency }}"
                                            data-yearcd="{{ $pledge->yearcd }}">
                                            {{ $pledge->number_of_charities }} {{ $pledge->number_of_charities > 1 ? 'charities' : 'charity' }} 
                                        </a>
                                   
                                    @endswitch
                                @else
                                    <a type="button" class="more-info "
                                        data-source="{{ $pledge->source  }}"
                                        data-type="{{ $pledge->donation_type }}"
                                        data-id="{{ $pledge->id }}"
                                        data-frequency="{{ $pledge->frequency }}"
                                        data-yearcd="{{ $pledge->yearcd }}">
                                        {{ $pledge->number_of_charities }} {{ $pledge->number_of_charities > 1 ? 'charities' : 'charity' }} 
                                    </a>
                                @endif
                                </td>

                                {{-- @if ($pledge->donation_type == 'Special Campaign')
                                    <td>{{ $pledge->region }} </td>
                                @else 
                                    <td></td>
                                @endif --}}

                            @endif
                            <td style="width: 10%">{{ $pledge->frequency }} </td>
                            @php

                            @endphp
                            <td class="text-right" style="width: 15%">$ {{ number_format($pledge->pledge,2) }} </td>
                            <td class="text-right" style="width: 10%">
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
                @php
                foreach($pledges as $pledge){
                    if($pledge->donation_type == "Annual")
                    {
                        $ignore = false;
                        break;
                    }
                }
                @endphp
                @if($key > $currentYear || $ignore || isset($globalIgnore) || !$campaignYear->isOpen())
                    @php
                    if($key>$currentYear)
                        {
                            $globalIgnore = true;
                        }
                        $ignore = true;
                    @endphp
                @else

                    <a class="btn btn-primary" style="margin-left: auto; margin-right: auto; width: fit-content; display: block;" 
                                href="#" onclick="event.preventDefault(); document.getElementById('duplicate-form-{{ $pledge->id }}').submit();">
                                {{$ignore}}Duplicate this pledge
                    </a>

                {{-- <a style="margin-left: auto;
    margin-right: auto;
    width: fit-content;
    display: block;" href="{{ route('annual-campaign.duplicate', $pledge->id) }}"> 
                <button type="button" class="pl-5 pr-5 duplicate align-content-center btn-lg btn-primary"
                        data-source="{{ $pledge->source  }}"
                        data-type="{{ $pledge->donation_type }}"
                        data-id="{{ $pledge->id }}"
                        data-frequency="{{ $pledge->frequency }}"
                        data-yearcd="{{ $pledge->yearcd }}">{{$ignore}}Duplicate this pledge
                </button>
                </a> --}}
                    <form id="duplicate-form-{{ $pledge->id }}" action="{{ route('annual-campaign.duplicate', $pledge->id) }}" method="POST" style="display: none;">
                        <input type="hidden" name="_token" value="xCKpsdfPXubkUv8ucCslZObQaBH85OUTcMWS3mPS">
                        <input type="hidden" name="source" value="{{ $pledge->source }}">
                        <input type="hidden" name="type" value="{{ $pledge->donation_type }}">
                        <input type="hidden" name="id" value="{{ $pledge->id }}">
                        <input type="hidden" name="frequency" value="{{ $pledge->frequency }}">
                        <input type="hidden" name="yearcd" value="{{ $pledge->yearcd }}">
                    </form>
                @endif

            </div>
        </div>
    </div>

    @endforeach


</div>
