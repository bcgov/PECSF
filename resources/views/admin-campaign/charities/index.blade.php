@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <div class="d-flex mt-3">
        <h4>CRA Charities</h4>
        <div class="flex-fill"></div>


        {{-- <div class="d-flex">
            <div class="mr-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#charity-create-modal">
                    Add a New Value
                  </button>
            </div>
        </div> --}}
    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard">Back</a></p>
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
    
		<table class="table table-bordered" id="charity-table" style="width:100%">
			<thead>
				<tr>
                    <th>Registration No</th>
					<th>Charity Name</th>
                    <th>Status</th>
                    <th>Effective Date</th>
                    <th>Designation</th>
                    <th>Category</th>
                    <th>Province</th>
                    <th>Country</th>
                    <th>Action </th>
				</tr>
			</thead>
		</table>

	</div>    
</div>   

{{-- @include('admin-campaign.charities.partials.model-create') --}}
@include('admin-campaign.charities.partials.model-edit')
@include('admin-campaign.charities.partials.model-show')

@endsection


@push('css')

    
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">
    
	<style>
	#charity-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	} 
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

    #charity-edit-modal input[type="text"]:disabled,
    #charity-show-modal input[type="text"]:disabled {
        border: none;
        padding-left: 0px;
        background: none;
    }

    #charity-edit-modal input[type="checkbox"],
    #charity-show-modal input[type="checkbox"] {
        width: 18px;
        height: 18px;
    }

    
</style>
@endpush


@push('js')
 
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>
    
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
        var oTable = $('#charity-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            // "deferRender": true,
            // "bSortClasses": false,
            select: true,
                        fixedHeader: true,
            // 'order': [[0, 'asc']],
            ajax: {
                url: '{!! route('settings.charities.index') !!}',
                data: function (d) {
                }
            },
            columns: [
                {data: 'registration_number', className: "dt-nowrap" },
                {data: 'charity_name',  },
                {data: 'charity_status',  },
                {data: 'effdt', },
                {data: 'designation_code',  },
                {data: 'category_code',  },
                {data: 'province',   },
                {data: 'country', },
                {data: 'action', name: 'action', orderable: false, searchable: false},

            ],
            columnDefs: [
                    {
                    },
            ]
        });

        function delay(callback, ms) {
          var timer = 0;
          return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
              callback.apply(context, args);
            }, ms || 0);
          };
        }

        // Grab the datatables filter input box and alter how it is bound to events
        $("#charity-table_filter input")
            .unbind() // Unbind previous default bindings
            .bind("keyup", delay(function(e) { // Bind our desired behavior
                if ($(this).val().length > 0 ) {
                    oTable.search(this.value).draw();
                } else {
                    oTable.search("").draw();
                }
                return;
            },500))
            .bind("search", delay(function(e) { // Bind our desired behavior
                    oTable.search(this.value).draw();
                return;
            },500));
            

        // Model for creating new charity
        $('#charity-create-modal').on('show.bs.modal', function (e) {
            // do something...
            var fields = ['code', 'name', 'status', 'effdt', 'notes'];
            $.each( fields, function( index, field_name ) {
                $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                $(document).find('[name='+field_name+']').val('');
            });
            $('#charity-create-modal').find('[name=status]').val('A');

        })

        $(document).on("click", "#create-confirm-btn" , function(e) {
		
            var form = $('#charity-create-model-form');
            var id = e.target.value;
            
            info = 'Are you sure to create this record?';
            if (confirm(info))
            {
                    
                var fields = ['code', 'name', 'status', 'effdt', 'notes'];
                $.each( fields, function( index, field_name ) {
                    $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                });

                $.ajax({
                    method: "POST",
                    url:  '{{ route('settings.charities.store')  }}',
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        oTable.ajax.reload(null, false);	// reload datatables
                        $('#charity-create-modal').modal('hide');
                        
                        var code = $("#charity-create-model-form [name='code']").val();
                        Toast('Success', 'Region code ' + code +  ' was successfully created.', 'bg-success' );
                    },
                    error: function(response) {
                        if (response.status == 422) {
                            
                            $.each(response.responseJSON.errors, function(field_name,error){
                                $(document).find('#charity-create-model-form [name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                            })
                        }
                        console.log('Error');
                    }
                });
            
            };
        });

        // Model -- Edit 
    	$(document).on("click", ".edit-charity" , function(e) {
			e.preventDefault();

            id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url:  '/settings/charities/' + id  + '/edit',
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        $(document).find('#charity-edit-model-form [name='+field_name+']').val(field_value);

                        if (field_name == 'use_alt_address') {
                            if (field_value > 0)
                                $('#charity-edit-model-form input[name=use_alt_address]').prop('checked', true);
                            else 
                                $('#charity-edit-model-form input[name=use_alt_address]').prop('checked', false);
                            console.log(field_value);
                        }   
                    });
                    
                    $('#charity-edit-modal').modal('show');
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
                            delay: 3000,
                            body: toast_body
            });
        }

        // Toast.fire({
        //                     icon: 'success',
                            
        //                     title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        //                 });

        $(document).on("click", "#save-confirm-btn" , function(e) {
		
            var form = $('#charity-edit-model-form');
            var id = $("#charity-edit-model-form [name='id']").val();
            
            info = 'Confirm to update this record?';
            if (confirm(info))
            {
                var fields = ['alt_address1', 'alt_address2', 'alt_city', 'alt_province', 'alt_postal_code', 
                            'alt_country', 'financial_contact_name', 'financial_contact_title',
                            'financial_contact_email'];
                $.each( fields, function( index, field_name ) {
                    $('#charity-edit-model-form [name='+field_name+']').nextAll('span.text-danger').remove();
                });

                $.ajax({
                    method: "PUT",
                    url:  '/settings/charities/' + id, 
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        oTable.ajax.reload(null, false);	// reload datatables
                        $('#charity-edit-modal').modal('hide');

                        var code = $("#charity-edit-model-form [name='registration_number']").val();
                        Toast('Success', 'Charity ' + code +  ' was successfully updated.', 'bg-success' );
                        
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
    	$(document).on("click", ".show-charity" , function(e) {
			e.preventDefault();

            id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '/settings/charities/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        // console.log(field_name);
                        $(document).find('#charity-show-model-form [name='+field_name+']').val(field_value);

                        if (field_name == 'use_alt_address') {
                            if (field_value > 0)
                                $('#charity-show-model-form input[name=use_alt_address]').prop('checked', true);
                            else 
                                $('#charity-show-model-form input[name=use_alt_address]').prop('checked', false);
                            console.log(field_value);
                        }
                    });
                    
                    $('#charity-show-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});

    });
    </script>
@endpush
