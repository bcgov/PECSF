@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <h4 class="mx-1 mt-3">Daily Campaign Summary Maintenance</h4>

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>
        </div>

        <div class="d-flex">
            <div class="mr-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bu-create-modal">
                    Add a New Record
                </button>
            </div>
        </div>
    </div>

@endsection
@section('content')

<div class="card">
	<div class="card-body">

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

		<table class="table table-bordered" id="bu-table" style="width:100%">
			<thead>
				<tr>
                    <th>Campaign Year</th>
					<th>As of Date</th>
                    <th>No of Donors</th>
                    <th>Total Amount</th>
                    <th>Action</th>
				</tr>
			</thead>
		</table>

	</div>
</div>

@include('admin-campaign.challenge-summary.partials.model-create')
@include('admin-campaign.challenge-summary.partials.model-edit')
@include('admin-campaign.challenge-summary.partials.model-show')

@endsection


@push('css')


    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
	#bu-table_filter label {
		text-align: right !important;
        padding-right: 10px;
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
        var oTable = $('#bu-table').DataTable({
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
                url: '{!! route('settings.challenge-summary.index') !!}',
                data: function (d) {
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
                {data: 'donors', name: 'donors', className: "dt-nowrap" },
                {data: 'dollars', name: 'dollars', className: "dt-nowrap" },
                {data: 'action', name: 'action', className: "dt-nowrap", orderable: false, searchable: false}
            ],
            columnDefs: [
                    {
                        width: '5em',
                        targets: [0]
                    },
                    {
                        render: DataTable.render.number(',', '', 0, ''),
                        targets: [2],
                    },
                    {
                        render: DataTable.render.number(',', '', 0, '$ '),
                        targets: [3],
                    },
            ]
            
        });

        // Model for creating new business unit
        $('#bu-create-modal').on('show.bs.modal', function (e) {
            // do something...
            var fields = ['campaign_year', 'as_of_date', 'donors', 'dollars', 'notes'];
            $.each( fields, function( index, field_name ) {
                $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                $(document).find('[name='+field_name+']').val('');
            });
            $('#bu-create-modal').find('[name=status]').val('A');

        })

        $(document).on("click", "#create-confirm-btn" , function(e) {

            var form = $('#bu-create-model-form');
            var id = e.target.value;

            info = 'Are you sure to create this record?';
            if (confirm(info))
            {

                var fields = ['campaign_year', 'as_of_date', 'donors', 'dollars', 'notes'];
                $.each( fields, function( index, field_name ) {
                    $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                });

                $.ajax({
                    method: "POST",
                    url:  '{{ route('settings.challenge-summary.store')  }}',
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        oTable.ajax.reload(null, false);	// reload datatables
                        $('#bu-create-modal').modal('hide');

                        var code = $("#bu-create-model-form [name='campaign_year']").val();
                        Toast('Success', 'The campaign year ' + code + ' -  Daily Campaign Summary record was successfully created.', 'bg-success' );
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

        // Model -- Edit
    	$(document).on("click", ".edit-bu" , function(e) {
			e.preventDefault();

            var fields = ['campaign_year', 'as_of_date', 'donors', 'dollars', 'notes'];
            $.each( fields, function( index, field_name ) {
                $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
            });

            id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url:  '/settings/challenge-summary/' + id  + '/edit',
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        $(document).find('#bu-edit-model-form [name='+field_name+']').val(field_value);
                    });
                    $('#bu-edit-modal').modal('show');
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
                var fields = ['campaign_year', 'as_of_date', 'donors', 'dollars', 'notes'];
                $.each( fields, function( index, field_name ) {
                    $('#bu-edit-model-form [name='+field_name+']').nextAll('span.text-danger').remove();
                });

                $.ajax({
                    method: "PUT",
                    url:  '/settings/challenge-summary/' + id,
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        oTable.ajax.reload(null, false);	// reload datatables
                        $('#bu-edit-modal').modal('hide');

                        var code = $("#bu-edit-model-form [name='campaign_year']").val();
                        Toast('Success', 'The campaign year ' + code + ' -  Daily Campaign Summary record was successfully updated.', 'bg-success' );

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

            var fields = ['campaign_year', 'as_of_date', 'donors', 'dollars', 'notes'];
            $.each( fields, function( index, field_name ) {
                $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
            });

            id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '/settings/challenge-summary/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        // console.log(field_name);
                        $(document).find('#bu-show-model-form [name='+field_name+']').val(field_value);
                    });
                    $('#bu-show-modal').modal('show');
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
            code = $(this).attr('data-code');

            Swal.fire( {
                title: 'Are you sure you want to delete campain year "' + code + '" daily camapign summary record" ?',
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
                        url:  '/settings/challenge-summary/' + id,
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            Toast('Success', 'The campaign year ' + code + ' -  Daily Campaign Summary record was successfully deleted.', 'bg-success' );
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
