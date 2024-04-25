@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <h4 class="mx-1 mt-3">Eligible Employee Summary Maintenance</h4>

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>
        </div>

        <div class="d-flex">
            <div class="mr-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ee-create-modal">
                    Add a New Record
                </button>
            </div>
        </div>
    </div>

@endsection
@section('content')

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="card">
    <form class="filter">
        <div class="card-body py-0 search-filter">
            <h2>Search Criteria</h2>
    
            <div class="form-row">

                <div class="form-group col-md-2">
                    <label for="filter_campaign_year">
                        Campaign Year
                    </label>
                    <select class="form-control" name="filter_campaign_year">
                        <option value="" selected>All</option>
                        @foreach ($campaign_years as $cy)                                            
                            <option value="{{ $cy }}">{{ $cy }}</option>
                        @endforeach
                    </select>
                </div> 

                <div class="form-group col-md-3">
                    <label for="filter_organization_code">
                        Organization
                    </label>
                    <select name="filter_organization_code" value="" class="form-control">
                        <option value="">All</option>
                        @foreach( $organizations as $organization)
                            <option value="{{ $organization->code }}"
                                {{ isset($filter['filter_organization_code']) && $filter['filter_organization_code'] == $organization->code ? 'selected' : '' }}>
                                {{ $organization->name }} ({{ $organization->code }})</option>
                        @endforeach 
                    </select>
                </div>
    
                <div class="form-group col-md-3">
                    <label for="filter_business_unit">
                        Business Code / Name
                    </label>
                    <input name="filter_business_unit" class="form-control" 
                            value="{{ isset($filter['filter_business_unit']) ? $filter['filter_business_unit'] : '' }}"/>
                </div> 
    
                <div class="form-group col-md-1">
                    <label for="search">
                        &nbsp;
                    </label>
                    <button type="button" id="refresh-btn" value="Search" class="form-control btn-primary">Search</button>
                </div>
                <div class="form-group col-md-1">
                    <label for="search">
                        &nbsp;
                    </label>
                    <button type="button" id="reset-btn" value="Reset" class="form-control  btn-secondary" >Clear</button>
                </div>

            </div>
        </div> 
    </form>  
</div>   

<div class="card">
	<div class="card-body">

		<table class="table table-bordered" id="ee-table" style="width:100%">
			<thead>
				<tr>
                    <th>Campaign Year</th>
					<th>As of Date</th>

                    <th>Organization</th>
                    <th>Business Unit</th>
                    <th>Name</th>
                    <th>Employee Count</th>
                    <th>Action</th>
				</tr>
			</thead>
		</table>

	</div>
</div>

@include('admin-campaign.eligible-employee-summary.partials.model-create')
@include('admin-campaign.eligible-employee-summary.partials.model-edit')
@include('admin-campaign.eligible-employee-summary.partials.model-show')

@endsection


@push('css')


    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
	#ee-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}

    #ee-table_filter {
        display: none;
    }

    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

    div.dataTables_wrapper div.dataTables_processing {
      top: 5%;
    }

    </style>
@endpush


@push('js')

    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 3000);

    $(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        // Datatables
        var oTable = $('#ee-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: true,
            select: true,
            'order': [[0, 'desc']],
            ajax: {
                url: '{!! route('settings.eligible-employee-summary.index') !!}',
                data: function (data) {
                    data.filter_organization_code  = $('select[name=filter_organization_code').val();
                    data.filter_campaign_year = $('select[name=filter_campaign_year').val();
                    data.filter_business_unit = $('input[name=filter_business_unit').val();
                },
                complete: function(xhr, resp) {
                    min_height = $(".wrapper").outerHeight();
                    $(".main-sidebar").css('min-height', min_height);
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
                {data: 'campaign_year', name: 'campaign_year', className: "dt-nowrap" },
                {data: 'as_of_date', name: 'as_of_date', className: "dt-nowrap" },
                {data: 'organization_code', name: 'organization_code', className: "dt-nowrap" },
                {data: 'business_unit_code', name: 'business_unit_code', className: "dt-nowrap" },
                {data: 'business_unit_name', name: 'business_unit_name', className: "dt-nowrap" },
                {data: 'ee_count', name: 'ee_count', className: "dt-center dt-nowrap" },
                {data: 'action', name: 'action', className: "dt-nowrap", orderable: false, searchable: false}
            ],
            columnDefs: [
                    {
                        width: '5em',
                        targets: [0]
                    },
                    // {
                    //     render: DataTable.render.number(',', '', 0, '$ '),
                    //     targets: [3],
                    // },
                    {
                        render: DataTable.render.number(',', '', 0, ''),
                        targets: [5],
                    },
            ]
            
        });

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                if ($(this).parents('form.filter') )  
                     oTable.ajax.reload();    
                return false;
            }
        });

        $('.search-filter select').on('change', function() {
            oTable.draw();
        });

        $('#refresh-btn').on('click', function() {
            // oTable.ajax.reload(null, true);
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {

            // Reset filter fields value
            $('.search-filter input').map( function() {$(this).val(''); });
            $('.search-filter select').map( function() { return $(this).val(''); })

            oTable.search( '' ).columns().search( '' ).draw();
        });

        // Model for creating new business unit
        $('#ee-create-modal').on('show.bs.modal', function (e) {
            // do something...
            var fields = ['campaign_year', 'as_of_date', 'donors', 'dollars', 'notes'];
            $.each( fields, function( index, field_name ) {
                $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                $(document).find('[name='+field_name+']').val('');
            });
            $('#ee-create-modal').find('[name=campaign_year]').val( {{ today()->year }} );
            $('#ee-create-modal').find('[name=status]').val('A');

        })

        $(document).on("click", "#create-confirm-btn" , function(e) {

            var form = $('#bu-create-model-form');
            var id = e.target.value;

            info = 'Are you sure to create this record?';
            if (confirm(info))
            {

                var fields = ['campaign_year', 'as_of_date', 'organization_code', 'ee_count', 'notes'];
                $.each( fields, function( index, field_name ) {
                    $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                });

                $.ajax({
                    method: "POST",
                    url:  '{{ route('settings.eligible-employee-summary.store')  }}',
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        oTable.ajax.reload(null, false);	// reload datatables
                        $('#ee-create-modal').modal('hide');

                        var year = $("#bu-create-model-form [name='campaign_year']").val();
                        var code = $("#bu-create-model-form [name='business_unit']").val();
                        Toast('Success', 'The campaign year "' + year + '" and business unit "' + code + '" - Eligible Employee Summary record was successfully created.', 'bg-success' );
                    },
                    error: function(response) {
                        if (response.status == 422) {

                            $.each(response.responseJSON.errors, function(field_name,error){
                                $(document).find('#bu-create-model-form [name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                            })
                        }
                        console.log('Error');
                    }
                });

            };
        });

        $('#ee-create-modal select[name="organization_code"]').on('change', function (e) {
            name = $(this).find('option:selected').attr('data-bu');
            $('#ee-create-modal input[name="business_unit"]').val( name );
        });


        // Model -- Edit
    	$(document).on("click", ".edit-bu" , function(e) {
			e.preventDefault();

            var fields = ['campaign_year', 'as_of_date', 'organization_code', 'ee_count', 'notes'];
            $.each( fields, function( index, field_name ) {
                $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
            });

            id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url:  '/settings/eligible-employee-summary/' + id  + '/edit',
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        $(document).find('#bu-edit-model-form [name='+field_name+']').val(field_value);
                    });
                    $('#ee-edit-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});

        function Toast( toast_title, toast_body, toast_class) {
            $(document).Toasts('create', {
                            class: toast_class,
                            title: toast_title,
                            autohide: true,
                            delay: 6000,
                            body: toast_body
            });
        }

        // Toast.fire({
        //                     icon: 'success',

        //                     title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        //                 });

        $(document).on("click", "#save-confirm-btn" , function(e) {

            var form = $('#bu-edit-model-form');
            var id = $("#bu-edit-model-form [name='id']").val();

            info = 'Confirm to update this record?';
            if (confirm(info))
            {
                var fields = ['campaign_year', 'as_of_date', 'organization_code', 'ee_count', 'notes'];
                $.each( fields, function( index, field_name ) {
                    $('#bu-edit-model-form [name='+field_name+']').nextAll('span.text-danger').remove();
                });

                $.ajax({
                    method: "PUT",
                    url:  '/settings/eligible-employee-summary/' + id,
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        oTable.ajax.reload(null, false);	// reload datatables
                        $('#ee-edit-modal').modal('hide');

                        var year = $("#bu-edit-model-form [name='campaign_year']").val();
                        var code = $("#bu-edit-model-form [name='business_unit_code']").val();
                        Toast('Success', 'The campaign year "' + year + '" and business unit "' + code + '" - Eligible Employee Summary record was successfully updated.', 'bg-success' );

                    },
                    error: function(response) {
                        if (response.status == 422) {

                            $.each(response.responseJSON.errors, function(field_name,error){
                                $(document).find('[name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                            })
                        }
                        console.log('Error');
                    }
                });

            };
        });

        // Model -- Show
    	$(document).on("click", ".show-bu" , function(e) {
			e.preventDefault();

            var fields = ['campaign_year', 'as_of_date', 'organization_code', 'ee_count', 'notes'];
            $.each( fields, function( index, field_name ) {
                $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
            });

            id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '/settings/eligible-employee-summary/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        // console.log(field_name);
                        $(document).find('#bu-show-model-form [name='+field_name+']').val(field_value);
                    });
                    $('#ee-show-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});

        // Model -- Delete
        $(document).on("click", ".delete-bu" , function(e) {
            e.preventDefault();

            id = $(this).attr('data-id');
            year = $(this).attr('data-year');
            code = $(this).attr('data-code');

            Swal.fire( {
                title: 'Are you sure you want to delete campaign year "' + year + '" and business unit "' + code + '" eligible employee summary record ?',
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
                        url:  '/settings/eligible-employee-summary/' + id,
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            Toast('Success', 'The campaign year "' + year + '" and business unit "' + code + '" - Eligible Employee Summary record was successfully deleted.', 'bg-success' );
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
                } else if (result.isCancelledDenied) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })
        });

    });
    </script>
@endpush
