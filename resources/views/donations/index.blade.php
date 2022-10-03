@extends('adminlte::page')
@section('content_header')

    {{-- Show when Campaign Year Open --}}
    <div class="d-flex justify-content-center pt-3">
        <div class="card border-warning text-center" style="background:#D9EAF7;max-width: 50em; border-radius: 1em;">
            <div class="card-body" style="color:#1a5a96;">
                <h5 class="card-title"></h5>
                @if ( $campaignYear->isOpen() )
                    <p class="card-text text-left">
                        From {{ $campaignYear->start_date->format('F jS') }} - {{ $campaignYear->end_date->format('F jS') }} we are in a period of open enrolment for the PECSF Campaign.
                        The choices you make and save by end of day {{ $campaignYear->end_date->format('F jS')}} will begin with your first pay period in January.
                    </p>
                    @if ($current_pledge)
                        <p class="card-text text-left">
                            To make changes to your proposed pledge, click into the box below where your {{ $campaignYear->calendar_year }} choices are shown.
                        </p>
                        <a href="{{ route('donate') }}" class="btn btn-primary">Make change to your PECSF pledge</a>
                    @else
                        <p class="card-text text-left">
                            To make a pledge click the Donate button, copy a prior years choices from your Donation History.
                        </p>
                        <a href="{{ route('donate') }}" class="btn btn-primary">Donate</a>
                    @endif
                @else

                    <p class="font-weight-bold">Thank you for choosing to support PECSF!</p>
                    <p class="card-text text-left">Click the “Details” button below to see your campaign pledge.</p>
                    <p class="card-text text-left">
                        {{-- The Fall campaign has closed, to make changes to your PECSF pledge please email PECSF@gov.bc.ca --}}
                        If you need to change or stop your PECSF campaign payroll pledge deduction, please email <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>.
                        {{-- Click the detail button below to see your campaign pledge in VIEW mode.    --}}
                    </p>
                    <p>
                        To make a new one-time donation outside of campaign, click <span class="font-weight-bold">“Donate to PECSF Now”</span> below.
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex mt-3">
        <h1>My Donations</h1>
        @if($totalPledgedDataTillNow > 0)
            <div class="flex-fill"></div>
            @if (!$campaignYear->isOpen() )
                <x-button :href="route('donate-now.index')">Donate to PECSF Now</x-button>
            @endif
            <x-button style="outline-primary" class="ml-2" data-toggle="modal" data-target="#learn-more-modal" >Why donate to PECSF?</x-button>
        @endif
    </div>
    <div class="d-flex flex-column">
        <p class="m-0">
            Since you started giving through PECSF, you've donated ${{ number_format($totalPledgedDataTillNow,0) }}, as BC Public Servant.
        </p>
        <small>reflects pledge totals from 2005 onwards</small>
    </div>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-center justify-content-lg-start mb-2" role="tablist">
            <div class="px-4 py-1 mr-2 border-bottom border-primary">
                <x-button role="tab" href="#" style="">
                    Donation History
                </x-button>
            </div>
        </div>
        {{-- @if($pledges->count() > 0) --}}
        {{-- @if ($old_pledges_by_yearcd->count() > 0 or $old_bi_pledges_by_yearcd->count() > 0 ) --}}
        @if ($pledges_by_yearcd->count() > 0)
            @include('donations.partials.history')
        @else
        <div class="text-center text-primary">
            <p>
                <strong>No Campaign has been started yet.</strong>
            </p>
            <p>
                You do not have any active campaigns right now. <br>
                Click on one of the options below to get started!
            </p>
            <x-button :href="route('donate-now.index')">Donate to PECSF Now</x-button>
            <p class="pt-3">
                OR
            </p>
            <x-button style="link" data-toggle="modal" data-target="#learn-more-modal">Learn more about donating to PECSF.</x-button>
        </div>
        @endif
        <div class="justify-content-center">
            <a href="{{route('donations.list')}}?download_pdf=true"><button style="background:#fff;margin-left:auto;margin-right:auto;display:block;width:40%;border:#12406b 1px solid;padding:8px;text-align:center;">Export Summary</button></a>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="pledgeDetailModal" tabindex="-1" role="dialog" aria-labelledby="pledgeDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header bg-primary">
            <h5 class="modal-title" id="pledgeDetailModalTitle">Pledge Detail
                    <span class="text-dark font-weight-bold"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>

@include('donations.partials.learn-more-modal')


@push('js')
<script>
    $('#learn-more-modal').on('slide.bs.carousel', function (e) {
        if(e.to == 0) {
            $(this).find(".prev-btn").addClass("d-none");
        }
        else if (e.to === 5) {
            $(this).find(".next-btn").addClass("d-none");
            $(this).find(".ready-btn").removeClass("d-none");
        } else {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".next-btn").removeClass("d-none")
            $(this).find(".ready-btn").addClass("d-none");
        }
    })

    $('.more-info').click( function(event) {
        event.stopPropagation();
        // var current_id = event.target.id;
        yearcd = $(this).data('yearcd');
        frequency = $(this).data('frequency');
        source = $(this).data('source');
        donation_type = $(this).data('type');
        id  = $(this).data('id');

        target = '.modal-body';
        $(target).html('');

        console.log( 'more info - ' );
        if ( yearcd  ) {
            // Lanuch Modal page for listing the Pool detail
            $.ajax({
                url: '{{ route("donations.pledge-detail") }}',
                type: 'GET',
                data: 'yearcd='+ yearcd + '&frequency='+ frequency +'&source='+ source + '&id='+id+ '&donation_type='+donation_type   ,
                dataType: 'html',
                success: function (result) {
                    // $('.modal-title span').html(name);
                    $(target).html(result);
                },
                complete: function() {
                },
                error: function () {
                    alert("error");
                    $(target).html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
                }
            })

            $('#pledgeDetailModal').modal('show')
        }
    });


</script>
@endpush
@endsection
