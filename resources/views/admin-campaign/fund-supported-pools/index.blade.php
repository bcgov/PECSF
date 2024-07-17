@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <h4 class="mx-1 mt-3">Fund Supported Pools</h4>

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>
        </div>

        <div class="d-flex">
            <div class="mr-2">
                <x-button class="btn-primary" :href="route('settings.fund-supported-pools.create')">Add New Fund Supported Pool</x-button>
            </div>
        </div>
    </div>

@endsection
@section('content')

<form id="fspool-filter-form" method="post">
    <div class="card search-filter">

        <div class="card-body pb-0 ">
            <h2 class="text-primary">Search Criteria</h2>
            <p>Enter any informartion you have and click Search. Leave fields blank for a list of all values.</p>

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="region_id">
                        Region
                    </label>
                    <select name="region_id" value="" class="form-control">
                        <option value="">All</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}"
                                {{ isset($filter['region_id']) && $filter['region_id'] == $region->id ? 'selected' : '' }}>
                                {{ $region->code }} - {{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label for="start">
                        Start Date (>=)
                    </label>
                    <input type="date" name="start_date"  class="form-control"
                            value="{{ isset($filter['start_date']) ? $filter['start_date'] : '' }}" />
                </div>

                <div class="form-group col-md-2">
                    <label for="status">
                        Status
                    </label>
                    <select name="status" value="" class="form-control">
                        <option value="" {{ isset($filter['status']) && $filter['status'] == '' ? 'selected' : '' }}>All</option>
                        <option value="A" {{ isset($filter['status']) && $filter['status'] == 'A' ? 'selected' : '' }}>Active</option>
                        <option value="I" {{ isset($filter['status']) && $filter['status'] == 'I' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label for="effective_type">
                        Effective Type
                    </label>
                    <select name="effective_type" class="form-control ">
                        <option value="" {{ isset($filter['effective_type']) && $filter['effective_type'] == '' ? 'selected' : '' }}>Show All</option>
                        <option value="C" {{ isset($filter['effective_type']) && $filter['effective_type'] == 'C' ? 'selected' : '' }}>Current</option>
                        <option value="F" {{ isset($filter['effective_type']) && $filter['effective_type'] == 'F' ? 'selected' : '' }}>Future</option>
                        <option value="H" {{ isset($filter['effective_type']) && $filter['effective_type'] == 'H' ? 'selected' : '' }}>History</option>
                    </select>
                </div>

                <div class="form-group col-md-1">
                    <label for="search">
                        &nbsp;
                    </label>
                    <button type="button" id="refresh-btn" value="Search" class="form-control btn btn-primary">Search</button>
                </div>
                <div class="form-group col-md-1">
                    <label for="search">
                        &nbsp;
                    </label>
                    <button type="button" id="reset-btn" value="Clear" class="form-control btn btn-secondary">Clear</button>
                </div>

            </div>

        </div>
    </div>
</form>



<div class="card">
	<div class="card-body">

        {{-- @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif --}}

        {{-- <div class="category-filter">
            <select id="effectiveTypeFilter" class="form-control form-control-sm ml-1">
                <option value="" selected>Show All</option>
                <option value="C">Current</option>
                <option value="F">Future</option>
                <option value="H">History</option>
            </select>
        </div> --}}

        <table class="table table-bordered" id="fspool-table" style="width:100%">
            <thead>
                <tr>
                    <th>Region Code</th>
                    <th>Region Name</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    {{-- <th>Effective Type</th> --}}
                    <th>Number of Charity</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>



	</div>
</div>

@include('admin-campaign.fund-supported-pools.partials.modal-duplicate')

@endsection


@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">

	<style>

    #fspool-table_filter {
        display: none;
    }
	#fspool-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

    div.dataTables_wrapper div.dataTables_processing {
      top: 5%;
    }

    /* select.form-control{
     display: inline;
        width: 200px;
        margin-left: 25px;
    } */

</style>
@endpush


@push('js')

    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}" ></script>

    <script>
    // window.setTimeout(function() {
    //     $(".alert-success").fadeTo(500, 0).slideUp(500, function(){
    //         $(this).remove();
    //     });
    // }, 5000);


    $(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        // Datatables
        var oTable = $('#fspool-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            stateSave: true,
            serverSide: true,
            select: true,
            'order': [[0, 'asc'], [2, 'asc']],
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw(false);

                @if (!(str_contains( url()->previous(), 'settings/fund-supported-pools')))
                    oTable.page( 'first' ).draw( 'page' );
                @endif
            },
            ajax: {
                url: '{!! route('settings.fund-supported-pools.index') !!}',
                data: function (d) {
                    d.region_id = $("select[name='region_id']").val();
                    d.start_date = $("input[name='start_date']").val();
                    d.status = $("select[name='status']").val();
                    d.effective_type = $("select[name='effective_type']").val();
                },
                error: function(xhr, resp, text) {
                        if (xhr.status == 401) {
                            { // session expired
                                window.location.href = '/login';
                            }
                        }
                },
            },
            columns: [
                {data: 'region.code', name: 'region.code', className: "dt-nowrap", orderData: [0, 2], },
                {data: 'region.name', name: 'region.name', className: "dt-nowrap", orderData: [1, 2], },
                {data: 'start_date', name: 'start_date', className: "dt-nowrap",  orderData: [0, 2], },
                {data: 'status', name: 'status', className: "dt-nowrap", orderData: [3, 2], },
                // {data: 'effectiveType', name: 'effectiveType', className: "dt-nowrap", orderable: false, searchable: false, "visible": true },
                {data: 'charities', orderable: false, searchable: false, },
                {data: 'action', name: 'action', className: "dt-nowrap", orderable: false, searchable: false},
            ],
            columnDefs: [
                    {
                        render: function (data, type, full, meta) {
                            if (data == 'A') {
                                return 'Active';
                            } else {
                                return 'Inactive';
                            }
                        },
                        targets: 3
                    },
                    // {
                    //     render: function (data, type, full, meta) {
                    //         if (data == 'H') {
                    //             return 'History';
                    //         } else if (data == 'F') {
                    //             return 'Future';
                    //         } else {
                    //             return 'Current';
                    //         }
                    //     },
                    //     targets: 4
                    // },
                    {
                        render: function (data, type, full, meta) {
                            return data.length;
                        },
                        targets: 4
                    },
                    {
                        // width: '5em',
                        // targets: [1],
                    },
            ]
        });

        //
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                oTable.ajax.reload();
                return false;
            }
        });

        $("select[name='region_id'], input[name='start_date'], select[name='status'], select[name='effective_type']").on('change', function() {
            oTable.draw();
        });

        $('#refresh-btn').on('click', function() {
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {

            $('.search-filter input').map( function() {$(this).val(''); });
            $('.search-filter select').map( function() { return $(this).val(''); })

            oTable.search( '' ).columns().search( '' ).draw();
        });



        // $('#fspool-table_filter.dataTables_filter').append($("#effectiveTypeFilter"));

        // $('#effectiveTypeFilter').on('change', function () {
        //     oTable.order( [[0, 'asc'], [1, 'desc']] ).draw();
        // } );

        // Model -- Delete
        var delete_id = '';
        var delete_region = '';
        var delete_start_date = '';

        $(document).on("click", ".delete-pool" , function(e) {
            e.preventDefault();

            delete_id = $(this).attr('data-id');
            delete_region = $(this).attr('data-region');
            delete_start_date = $(this).attr('data-start-date');

            Swal.fire( {
                title: 'Are you sure you want to delete Fund Supported Pool "' + delete_region + '" with start date "' + delete_start_date + '" ?',
                text: 'This action cannot be undone.',
                // icon: 'question',
                //showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Delete',
                buttonsStyling: false,
                //confirmButtonClass: 'btn btn-danger',
                customClass: {
                	confirmButton: 'btn btn-danger', //insert class here
                    cancelButton: 'btn btn-secondary ml-2', //insert class here
                }
                //denyButtonText: `Don't save`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Swal.fire('Saved!', '', '')
                    $.ajax({
                        method: "DELETE",
                        url:  '/settings/fund-supported-pools/' + delete_id,
                        dataType: 'json',
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            // Toast('Success',  'The Fund Supported Pool "' + delete_region + '" with Start date "' + delete_start_date +
                            //                 '" was successfully deleted.', 'bg-success' );
                            toastr["success"]( 'The Fund Supported Pool "' + delete_region + '" with Start date "' + delete_start_date +
                                            '" has been successfully deleted.', '',
                                            {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
                        },
                        error: function(xhr, resp, text) {
                            if (xhr.status == 401 || xhr.status == 419) {
                                { // session expired
                                    window.location.href = '/login';
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON.title,
                                    text: xhr.responseJSON.message,
                                })
                                console.log(xhr.responseJSON.message);
                            }
                        }
                    });
                } else if (result.isDismissed) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })

        });


        // Model -- Duplicate
        var duplicate_id = '';
        var duplicate_region = '';
        var duplicate_start_date = '';

        $(document).on("click", ".duplicate-pool" , function(e) {
            e.preventDefault();

            duplicate_id = $(this).attr('data-id');
            duplicate_region = $(this).attr('data-region');
            duplicate_start_date = $(this).attr('data-start-date');

            $('#pool-duplicate-modal input[name="region"]').val( duplicate_region );
            $('#pool-duplicate-modal input[name="old_start_date"]').val( duplicate_start_date );

            $('#pool-duplicate-modal-button').attr('data-id', duplicate_id);

            // Clean up a previous error message if exists
            $('#pool-duplicate-modal-form .alert.alert-danger').text('');
            $('#pool-duplicate-modal-form .alert.alert-danger').hide();

            // Show modal window
            $('#pool-duplicate-modal').modal('show');

        });

                // Model for creating new region
        $('#pool-duplicate-modal').on('show.bs.modal', function (e) {
            // do something...
            var fields = ['start_date'];
            $.each( fields, function( index, field_name ) {
                $('#pool-duplicate-modal').find('[name='+field_name+']').nextAll('span.text-danger').remove();
                $('#pool-duplicate-modal').find('[name='+field_name+']').val('');
            });
        })

        $(document).on("click", "#pool-duplicate-modal-button" , function(e) {

            var fields = ['start_date'];
            $.each( fields, function( index, field_name ) {
                $('#pool-duplicate-modal').find('[name='+field_name+']').nextAll('span.text-danger').remove();
            });


            var form = $('#pool-duplicate-modal-form');
            var id = $(this).attr('data-id');
            var start_date = $('#pool-duplicate-modal-form input[name="start_date"]').val();

            $.ajax({
                url:  '/settings/fund-supported-pools/duplicate/' + id,
                method: 'post',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    oTable.ajax.reload(null, false);	// reload datatables
                    // Hide modal window
                    $('#pool-duplicate-modal').modal('hide');

                    // Display a message
                    // Toast('Success', 'The Fund Supported Pool "' + duplicate_region + '" with Start date "' + start_date +
                    //       '" was successfully created.', 'bg-success m-3');
                    toastr["success"]( 'The Fund Supported Pool "' + duplicate_region + '" with Start date "' + start_date +
                                            '" has been successfully created.', '',
                                            {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });                          

                },
                error: function(response) {
                    if (response.status == 422) {
                        $.each(response.responseJSON.errors, function(field_name,error){
                                $('#pool-duplicate-modal-form').find('[name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                        })
                        // $('#pool-duplicate-modal-form .alert.alert-danger').show();
                    } else {
                        console.log('Error');
                    }
                }
            });

        });

    });


@if ($message = Session::get('success'))
    $(function() {
        toastr["success"]( "{{ $message }}", '',
            {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
    });
@endif

    </script>


@endpush
