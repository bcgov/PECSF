@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <h4 class="mx-1 mt-3">Organizations</h4>

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>
        </div>

        <div class="d-flex">
            <div class="mr-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#organization-create-modal">
                    Add a New Organization
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
    
		<table class="table table-bordered" id="organization-table" style="width:100%">
			<thead>
				<tr>
                    <th>Organization Code</th>
					<th>Name</th>
                    <th>Status</th>
                    <th>Effective Date</th>
                    <th>Business Unit</th>
                    <th>Action</th>
				</tr>
			</thead>
		</table>

	</div>    
</div>   

@include('admin-campaign.organizations.partials.model-create')
@include('admin-campaign.organizations.partials.model-edit')
@include('admin-campaign.organizations.partials.model-show')

@endsection


@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">

	<style>
	#organization-table_filter label {
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
 
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>

    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}" ></script>

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
        var oTable = $('#organization-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: true,
            select: true,
            'order': [[0, 'asc']],
            ajax: {
                url: '{!! route('settings.organizations.index') !!}',
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
                {data: 'code', name: 'code', className: "dt-nowrap" },
                {data: 'name', name: 'name', className: "dt-nowrap" },
                {data: 'status', name: 'status', className: "dt-nowrap" },
                {data: 'effdt', name: 'effdt', className: "dt-nowrap"},
                {data: 'bu_code', name: 'bu_code', className: "dt-nowrap"},
                {data: 'action', name: 'action', className: "dt-nowrap", orderable: false, searchable: false}
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
                        targets: 2
                    },
                    {
                        width: '5em',
                        targets: [0,3]
                    },
            ]
        });

        // Switching the Oagnizaton Change
        $('#organization-create-modal input[name="code"], #organization-edit-modal input[name="code"]').on('change', function (e) {
            code = $(this).val();
            if (code.toUpperCase() == 'GOV') {
                $(this).parents('form').find('.bu_code_area').hide();
                $(this).parents('form').find('select[name="bu_code"]').val('');
            } else {
                $(this).parents('form').find('.bu_code_area').show();
            }
        });
        $('#organization-create-modal input[name="code"]').trigger( "change" );
        $('#organization-edit-modal input[name="code"]').trigger( "change" );
        
        

        // Model for creating new organization
        $('#organization-create-modal').on('show.bs.modal', function (e) {
            // do something...
            var fields = ['code', 'name', 'status', 'effdt', 'notes', 'bu_code'];
            $.each( fields, function( index, field_name ) {
                $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                $(document).find('[name='+field_name+']').val('');
            });
            $('#organization-create-modal').find('[name=status]').val('A');

        })

        $(document).on("click", "#create-confirm-btn" , function(e) {
		
            var form = $('#organization-create-model-form');
            var id = e.target.value;
            
            info = 'Are you sure to create this record?';
            if (confirm(info))
            {
                    
                var fields = ['code', 'name', 'status', 'effdt', 'notes', 'bu_code'];
                $.each( fields, function( index, field_name ) {
                    $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                });

                $.ajax({
                    method: "POST",
                    url:  '{{ route('settings.organizations.store')  }}',
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        oTable.ajax.reload(null, false);	// reload datatables
                        $('#organization-create-modal').modal('hide');
                        
                        var code = $("#organization-create-model-form [name='code']").val();
                        // Toast('Success', 'Organization code ' + code +  ' was successfully created.', 'bg-success' );
                        toastr["success"]( 'Organization code ' + code +  ' has been successfully created.', '',
                                        {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
                    },
                    error: function(response) {
                        if (response.status == 422) {
                            
                            $.each(response.responseJSON.errors, function(field_name,error){
                                $(document).find('#organization-create-model-form [name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                            })
                        }
                        console.log('Error');
                    }
                });
            
            };
        });

        // Model -- Edit 
    	$(document).on("click", ".edit-organization" , function(e) {
			e.preventDefault();

            id = $(this).attr('data-id');

            var fields = ['code', 'name', 'status', 'effdt', 'notes', 'bu_code'];
            $.each( fields, function( index, field_name ) {
                    $('#organization-edit-model-form [name='+field_name+']').nextAll('span.text-danger').remove();
            });

            $.ajax({
                method: "GET",
                url:  '/settings/organizations/' + id  + '/edit',
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        $(document).find('#organization-edit-model-form [name='+field_name+']').val(field_value);
                    });

                    code = $('#organization-edit-modal input[name="code"]').val();
                    if (code.toUpperCase() == 'GOV') {
                        $('#organization-edit-model-form').find('.bu_code_area').hide();
                        $('#organization-edit-model-form').find('select[name="bu_code"]').val('');
                    } else {
                        $('#organization-edit-model-form').find('.bu_code_area').show();
                    }

                    $('#organization-edit-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});

        $(document).on("click", "#save-confirm-btn" , function(e) {
		
            var form = $('#organization-edit-model-form');
            var id = $("#organization-edit-model-form [name='id']").val();
            
            info = 'Confirm to update this record?';
            if (confirm(info))
            {
                var fields = ['code', 'name', 'status', 'effdt', 'notes', 'bu_code'];
                $.each( fields, function( index, field_name ) {
                    $('#organization-edit-model-form [name='+field_name+']').nextAll('span.text-danger').remove();
                });

                $.ajax({
                    method: "PUT",
                    url:  '/settings/organizations/' + id, 
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        oTable.ajax.reload(null, false);	// reload datatables
                        $('#organization-edit-modal').modal('hide');

                        var code = $("#organization-edit-model-form [name='code']").val();
                        // Toast('Success', 'Organization code ' + code +  ' was successfully updated.', 'bg-success' );
                        toastr["success"]( 'Organization code ' + code +  ' has been successfully updated.', '',
                                 {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
                        
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
    	$(document).on("click", ".show-organization" , function(e) {
			e.preventDefault();

            id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '/settings/organizations/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        // console.log(field_name);
                        $(document).find('#organization-show-model-form [name='+field_name+']').val(field_value);
                    });

                    code = $('#organization-show-modal input[name="code"]').val();
                    if (code.toUpperCase() == 'GOV') {
                        $('#organization-show-model-form').find('.bu_code_area').hide();
                        $('#organization-show-model-form').find('select[name="bu_code"]').val('');
                    } else {
                        $('#organization-show-model-form').find('.bu_code_area').show();
                    }

                    $('#organization-show-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});

        // Model -- Delete
        $(document).on("click", ".delete-organization" , function(e) {
            e.preventDefault();

            id = $(this).attr('data-id');
            code = $(this).attr('data-code');

            Swal.fire( {
                title: 'Are you sure you want to delete organization code "' + code + '" ?',
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
                        url:  '/settings/organizations/' + id, 
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            // Toast('Success', 'Organization code ' + code +  ' was successfully deleted.', 'bg-success' );
                            toastr["success"]( 'Organization code ' + code +  ' has been successfully deleted.', '',
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
                } else if (result.isCancelledDenied) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })
        });

    });
    </script>
@endpush
