@extends('adminlte::page')

@section('content_header')
    <div class="d-flex mt-3">
        <h1>Event bank deposit form</h1>
        <div class="flex-fill"></div>
    </div>

    <div class="mx-1 pt-3">
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ url()->previous() }}'">
            Back
        </button>
    </div>

@endsection



@section('content')

    {{-- @include('volunteering.partials.form_tabs') --}}




            @include('volunteering.partials.form')



    <div class="modal fade" id="info-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-charity-name" id="charity-modal-label">Have you deposited your funds and scanned your completed PECSF Bank Deposit Form? </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="info-modal-body">
                        <div class="row">
                            <div  class="col-md-12">
                                Before you complete this eForm you need to:
                                <br>
                            </div>
                        </div>

                        <div class="row">
                            <br>
                        </div>

                        <div class="row">
                            <ol>
                                <li>Print the PECSF Event Bank Deposit Attachment Form.</li>
                                <li>Take the money to the bank or Service BC and ensure that it gets deposited into the appropriate account (see the PECSF Event Bank Deposit Attachment Form for account numbers).</li>
                                <li>Attach the bank deposit receipt to the PECSF Event Bank Deposit Attachment Form and scan it.</li>
                            </ol>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                If you have any questions, please contact the <a href="mailto:pecsf@gov.bc.ca" target="_blank">PECSF HQ team.</a>
                            </div>
                        </div>
                        <div class="row">
                         <br>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="button" id="complete-btn" value="Refresh" class="form-control btn btn-primary" data-dismiss="modal" aria-label="Complete eForm from Close">Complete eForm</button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" id="cancel-btn" value="Refresh" class="form-control btn btn-secondary" onclick="window.location='{{ url()->previous() }}';">Cancel</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('css')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

<style>
    .select2 {
        width:100% !important;
    }
    .select2-selection--multiple{
        overflow: hidden !important;
        height: auto !important;
        min-height: 38px !important;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }

    table tr{
        background:#fff;
    }


    /* Temporaily override the app.scss*/
    .btn:focus, button:focus{
        border: #fff 2px solid!important;
    }

    .custom.pagination .page-item {
        margin-top: 0;
        border: #a2adb7 1px solid;
    }

    .custom.pagination a {
        text-decoration: none !important;
    }   
      /* Should be override in app.scss */
    .content-wrapper a {
        text-decoration: underline !important; 
    }

    input[type='radio'] {
        accent-color: #1A5A96;
        height:20px; 
        width:20px; 
        vertical-align: middle;
    }
    input[name="keyword"] {
        border:#000 1px solid;
    }

</style>


@endpush
@push('js')

<script>
    
$(function () {

    // treat browser back button like the 'back' button in this wizard page
    history.pushState(null, null, this.location.href);
    window.addEventListener('popstate', function(event) {
        url = this.location.href;
        if (url.indexOf('bank_deposit_form')) {
            if ($('.modal').hasClass('show')) {
                history.pushState(null, null, location.href);
                $('.modal').modal('hide');
            } else {
                $("#cancel-btn").trigger("click");
            }
        }
    });

    // prevent spacebar to trigger the page scrolling
    $(document).on("keypress", function(e) {
        var $focusElem = $(":focus");
        if (e.which == 13 && $focusElem.is("input"))
            e.preventDefault();
    });

    // prevent spacebar to trigger the page scrolling
    $(document).on("keypress", function(e) {
        var $focusElem = $(":focus");
        if (e.which == 32 && !($focusElem.is("input") || $focusElem.attr("contenteditable") == "true"))
            e.preventDefault();
    });

@if (isset($skip_info_modal) && !($skip_info_modal) )
    $("#info-modal").on('shown.bs.modal', function(event) {
        // not setting focus to submit button
        $('#complete-btn').trigger('focus');
    });

    $("#info-modal").modal('show');
@endif


    $('.closeModalBtn').on('click', function() {
        $('#regionalPoolModal').modal('hide');
    });

    



        function disableOneTime() {
            var eventTypeDropdown = $('#event_type');

            // Enable all options
            eventTypeDropdown.find('option').prop('disabled', false);

            eventTypeDropdown.find('option[value="Cash One-Time Donation"]').prop('disabled', true);
            eventTypeDropdown.find('option[value="Cheque One-Time Donation"]').prop('disabled', true);

            // Set the selected index and update the displayed option text
            var selectedIndex = 0; // Index of the default option
            eventTypeDropdown.find('option').eq(selectedIndex).prop('selected', true);
            eventTypeDropdown.trigger('change');
        }


        $('#organization_code').change(function(e){
            var selectedOrganization = $(this).val();
            if (selectedOrganization !== 'GOV' && selectedOrganization !== 'RET') {
                disableOneTime();    
            } else {
                var eventTypeDropdown = $('#event_type');
                eventTypeDropdown.find('option').prop('disabled', false);
            }    
        });  


    // method_selection
    $('.method_selection').on('keyup', function(e) {
        // Enter or space key on Wizard STEP icon to forward and backward    
        var key  = e.key;
        if (key === ' ' || key === 'Enter') {
            e.preventDefault();
            $(this).find('input[name=charity_selection]').trigger('click');
        }
    });

    // // Enter or space key on Wizard STEP icon to forward and backward 
    // $('.form-pool').on('keyup', function(e) {
    //     // Enter or space key on Wizard STEP icon to forward and backward    
    //     var key  = e.key;
    //     if (key === ' ' || key === 'Enter') {
    //         e.preventDefault();
    //         $(this).find('input[name=regional_pool_id]').prop('checked',true);
    //     }
    // });

    $('.form-pool').on('click', function(e) {
     
        // $(this).find('input[name=regional_pool_id]').trigger('click');
        // $(this).AddClass('active');
    });

});
</script>
        <script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

        <script type="x-tmpl" id="organization-tmpl">
            @include('volunteering.partials.add-organization', ['index' => 'XXX','charity' => "YYY"] )
        </script>
            @include('volunteering.partials.add-event-js')
            @include('donate.partials.choose-charity-js')

@include('volunteering.partials.dropzone-js')    

@endpush