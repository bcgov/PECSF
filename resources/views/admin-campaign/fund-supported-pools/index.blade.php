@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Fund Supported Pools</h4>
        <div class="flex-fill"></div>

        <div class="d-flex">
            <div class="mr-2">
                <x-button :href="route('settings.fund-supported-pools.create')">Add a New Value</x-button>        
                {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#region-create-modal">
                    Add a New Value
                  </button> --}}
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
    
		<table class="table table-bordered" id="region-table" style="width:100%">
			<thead>
				<tr>
                    <th>Region</th>
					<th>Start Date</th>
                    <th>Status</th>
                    <th>Charities</th>
                    <th>Action</th>
				</tr>
			</thead>
		</table>

	</div>    
</div>   

@include('admin-campaign.fund-supported-pools.partials.modal-delete')




@endsection


@push('css')

    
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
	#region-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	} 
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

</style>
@endpush


@push('js')
 
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>
    window.setTimeout(function() {
        $(".alert-success").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 5000);
    

    $(function() {
        	
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        }); 

        // Datatables
        var oTable = $('#region-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            select: true,
            'order': [[0, 'asc']],
            ajax: {
                url: '{!! route('settings.fund-supported-pools.index') !!}',
                data: function (d) {
                }
            },
            columns: [
                {data: 'region.name', name: 'region.name', className: "dt-nowrap" },
                {data: 'start_date', name: 'start_date', className: "dt-nowrap" },
                {data: 'status', name: 'status', className: "dt-nowrap" },
                {data: 'charities'  },
                {data: 'action', name: 'action', className: "dt-nowrap", orderable: false, searchable: false}
            ],
            columnDefs: [
                    {
                        width: '5em',
                        targets: [0]
                    },
            ]
        });

        // // Model -- Delete
        var delete_id = '';
        var delete_region = '';
        var delete_start_date = '';

        $(document).on("click", ".delete-pool" , function(e) {
            e.preventDefault();

            delete_id = $(this).attr('data-id');
            delete_region = $(this).attr('data-region');
            delete_start_date = $(this).attr('data-start-date');

            $('#pool-delete-modal input[name="region"]').val( delete_region );
            $('#pool-delete-modal input[name="start_date"]').val( delete_start_date );
            
            // $('#pool-delete-modal-form').attr('action', '/settings/fund-supported-pools/' + id); 
            $('#pool-delete-modal-button').attr('data-id', delete_id);

            // Clean up a previous error message if exists
            $('#pool-delete-modal-form .alert.alert-danger').text('');
            $('#pool-delete-modal-form .alert.alert-danger').hide();

            // Show modal window
            $('#pool-delete-modal').modal('show');

        });

        $(document).on("click", "#pool-delete-modal-button" , function(e) {

            $.ajax({
                method: "DELETE",
                url:  '/settings/fund-supported-pools/' + $(this).attr('data-id'), 
                success: function(data)
                {
                    oTable.ajax.reload(null, false);	// reload datatables
                    // Hide modal window
                    $('#pool-delete-modal').modal('hide');

                    // Display a message
                    Toast('Success', 'The Fund Supported Pool "' + delete_region + '" with Start date "' + delete_start_date +
                          '" was successfully deleted.', 'bg-success m-3');
                    
                },
                error: function(response) {
                    if (response.status == 422) {

                        //$('#pool-delete-modal-form .message').html(response.responseJSON.message);
                        $.each(response.responseJSON.errors, function(field_name,error){
                            $('#pool-delete-modal-form .alert.alert-danger').text( error );
                        });
                        $('#pool-delete-modal-form .alert.alert-danger').show();
                    } else {
                        console.log('Error');
                    }
                }
            });

        });

        function Toast( toast_title, toast_body, toast_class) { 
            $(document).Toasts('create', {
                icon: 'fas fa-solid fa-check',
                class: toast_class,
                title: toast_title,
                autohide: true,
                delay: 8000,
                body: toast_body
            });
        }

    });
    </script>


@endpush
