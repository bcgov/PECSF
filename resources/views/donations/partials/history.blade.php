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
                    @php $total = 0; $ignore = true; $pledge_for_duplicate = null; @endphp
                    @foreach($pledges as $pledge)
                        @php
                            if($pledge->donation_type == "Annual" && $ignore) {
                                $ignore = false;
                                $pledge_for_duplicate = $pledge;
                            }
                        @endphp
                        <tr class="">
                            <td style="width: 15%">
                                @switch($pledge->donation_type)
                                    @case('Donate Today')
                                        {{ 'Donate Now' }}
                                    @break
                                    @case('Donate Now')
                                        {{ 'Donate Now' }}
                                        @break
                                    @case('Event')
                                        {{ 'Annual' }}
                                        @break
                                    @default
                                        {{ $pledge->donation_type }}
                                @endswitch
                            </td>
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
                                            {{ count($pledge->charities) }} {{ count($pledge->charities) > 1 ? 'charities' : 'charity' }}




                                        </a>

                                    @endswitch
                                @else
                                    @if ($pledge->donation_type == 'Donate Today')
                                        {{ $pledge->number_of_charities }}
                                    @else
                                        <a type="button" class="more-info "
                                            data-source="{{ $pledge->source  }}"
                                            data-type="{{ $pledge->donation_type }}"
                                            data-id="{{ $pledge->id }}"
                                            data-frequency="{{ $pledge->frequency }}"
                                            data-yearcd="{{ $pledge->yearcd }}">
                                            {{ count($pledge->charities) }} {{ count($pledge->charities) > 1 ? 'charities' : 'charity' }}

                                        </a>
                                    @endif
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

                @if($key > $currentYear ||  $ignore  || $current_pledge || !$campaignYear->isOpen())
                    {{-- No 'duplicate' button --}}
                @else

                    <a class="duplicate-pledge btn btn-primary" style="margin-left: auto; margin-right: auto; width: fit-content; display: block;"
                                {{-- href="#"  --}}
                                data-id="{{ $pledge_for_duplicate->id }}"
                                data-source="{{ $pledge_for_duplicate->source }}"
                                {{-- onclick="event.preventDefault(); document.getElementById('duplicate-form-{{ $pledge->id }}').submit();" --}}
                                >
                                Duplicate this Annual Campaign pledge
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
                    <form id="duplicate-form-{{ $pledge_for_duplicate->id }}" action="{{ route('annual-campaign.duplicate', $pledge_for_duplicate->id) }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="source" value="{{ $pledge_for_duplicate->source }}">
                        <input type="hidden" name="type" value="{{ $pledge_for_duplicate->donation_type }}">
                        <input type="hidden" name="id" value="{{ $pledge_for_duplicate->id }}">
                        <input type="hidden" name="frequency" value="{{ $pledge_for_duplicate->frequency }}">
                        <input type="hidden" name="yearcd" value="{{ $pledge_for_duplicate->yearcd }}">
                    </form>
                @endif

            </div>
        </div>
    </div>

    @endforeach


</div>


@push('js')
<script>
$('a.duplicate-pledge').on('click', function(event){

    pledge_id = $(this).data("id");

    $.ajax({
        url: '/annual-campaign/valid-duplicate/' + pledge_id,
        data: 'source='+ $(this).data("source") ,
        type: 'GET',
        dataType: 'json',
        success: function (result) {
            // $('.modal-title span').html(name);
            if (result.message) {
                Swal.fire({
                    title: 'Are you sure ?',
                    text: result.message,
                    // icon: 'question',
                    //showDenyButton: true,
                    confirmButtonText: 'Continue',
                    showCancelButton: true,
                }).then((result) => {

                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $("#duplicate-form-"+ pledge_id ).submit();
                    }
                })
            } else {
                // submit form
                $("#duplicate-form-"+ pledge_id ).submit();
            }
        },
        complete: function() {
        },
        error: function () {
            alert("error");
            $(target).html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
        }
    })

});

</script>
@endpush
