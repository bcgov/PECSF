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

<form id="charity-filter-form" method="post">
    <div class="card search-filter">
    
        <div class="card-body pb-0 ">
            <h2 class="text-primary">Search Criteria</h2>
            <p>Enter any informartion you have and click Search. Leave fields blank for a list of all values.</p>

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="registration_number">
                        Registration No
                    </label>
                    <input name="registration_number"  class="form-control" />
                </div>
    
                <div class="form-group col-md-2">
                    <label for="name">
                        Charity Name
                    </label>
                    <input name="charity_name"   class="form-control" />
                </div>
    
                <div class="form-group col-md-2">
                    <label for="empl_status">
                        Status
                    </label>
                    <select name="charity_status" value="" class="form-control">
                        <option value="">All</option>
                        @foreach( $charity_status_list as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach 
                    </select>
                </div>
                
                <div class="form-group col-md-2">
                    <label for="effdt">
                        Effective Date 
                    </label>
                    <input type="date" name="effdt"  class="form-control" />
                </div>

                <div class="form-group col-md-2">
                    <label for="use_alt_address">
                        Use Alternate Address
                    </label>
                    <select name="use_alt_address" value="" class="form-control">
                        <option value="">All</option>
                        <option value="Y">Yes</option>
                        <option value="N">No</option>
                    </select>
                </div>
                
            </div>    
    
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="designation_code">
                        Designation
                    </label>
                    <select name="designation_code" value="" class="form-control">
                        <option value="">All</option>
                        @foreach( $designation_list as $key => $value)
                            <option value="{{ $key  }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="category_code">
                        Category
                    </label>
                    <select name="category_code"  value="" class="form-control">
                        <option value="">All</option>
                        @foreach( $category_list as $key => $value)
                            <option value="{{ $key }}">{{ $value }} </option>
                        @endforeach 
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="province">
                        Province
                    </label>
                    <select name="province"  value="" class="form-control">
                        <option value="">All</option>
                        @foreach( $province_list as $key => $value)
                            <option value="{{ $key }}">{{ $value }} </option>
                        @endforeach 
                    </select>
                </div>
    
    
                <div class="form-group col-md-1">
                    <label for="search">
                        &nbsp;
                    </label>
                    <button type="button" id="refresh-btn" value="Refresh" class="form-control btn btn-primary" />Search</button>
                </div>
                <div class="form-group col-md-1">
                    <label for="search">
                        &nbsp;
                    </label>
                    <button type="button" id="reset-btn" value="Reset" class="form-control btn btn-secondary">Reset</button>
                </div>
            </div>
    
        </div>    
    </div>
    </form>
    
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
    
        {{-- <div id="export-section" class="px-3 float-right">
            <button type="button" id="export-btn" value="export" class="btn btn-primary px-4 mb-2">Export</button>
            <span id="export-section-result"></span>
        </div> --}}

		<table class="table table-bordered" id="charity-table" style="width:100%">
			<thead>
				<tr>
                    <th></th>
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
        display:none;
    }
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
            'order': [[1, 'asc']],
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw();
            },
            ajax: {
                url: '{!! route('settings.charities.index') !!}',
                data: function (data) {
                    // data.term = $('#user').val();
                    data.registration_number = $("input[name='registration_number']").val();
                    data.charity_name = $("input[name='charity_name']").val();
                    data.charity_status = $("select[name='charity_status']").val();
                    data.effdt = $("input[name='effdt']").val();
                    data.designation_code = $("select[name='designation_code']").val();
                    data.category_code  = $("select[name='category_code']").val();
                    data.province = $("select[name='province']").val();
                    data.use_alt_address = $("select[name='use_alt_address']").val();
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
                {data: 'id', orderable: false, searchable: false, 
                        render: function (data, type, row, meta) {
                                       return meta.row + meta.settings._iDisplayStart + 1;
                        }
                },
                {data: 'registration_number', className: "dt-nowrap" },
                {data: 'charity_name',  },
                {data: 'charity_status',  },
                {data: 'effdt', },
                {data: 'designation_name',  },
                {data: 'category_name',      },
                {data: 'province',   },
                {data: 'country', },
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},

            ],
            columnDefs: [
            ]
        });


        // Move the export button to the filter area
        // $('#charity-table_filter').parent().append( $('#export-section') );

        $('#charity-filter-form').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                oTable.ajax.reload();
                return false;
            }
        });

        $('#refresh-btn').on('click', function() {
            // oTable.ajax.reload(null, true);
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {

            $('.search-filter input').map( function() {$(this).val(''); });
            $('.search-filter select').map( function() { return $(this).val(''); })

            oTable.search( '' ).columns().search( '' ).draw();
        });

        // // For auto-refresh 
        // var intervalID = null;
        // var batch_id = null;

        // $('#export-btn').on('click', function() {
            
        //     Swal.fire({
        //         text: 'Are you sure to export the selected data ?'  ,
        //         // icon: 'question',
        //         //showDenyButton: true,
        //         confirmButtonText: 'Export',
        //         showCancelButton: true,
        //     }).then((result) => {

        //         // Read more about isConfirmed, isDenied below 
        //         if (result.isConfirmed) {

        //             // refresh data tables first
        //             oTable.draw();
        //             $('#export-btn').prop('disabled', true);
        //             $('#export-section-result').html('Queued. Please wait.');

        //             var form = $('#charity-filter-form');

        //             // Use ajax call to submit
        //             $.ajax({
        //                 method: "GET",
        //                 dataType: 'json',
        //                 url: '/charities/export',
        //                 data: form.serialize(), // serializes the form's elements.
        //                 success: function(data) {
        //                     batch_id = data.batch_id;
        //                     console.log('export job submit');
        //                     intervalID = setInterval(exportProgress, 2000);
        //                 },
        //                 error: function(response) {
        //                     $('#export-btn').prop('disabled', false);
        //                     console.log('Error');
        //                 }
        //             });
        //         }

        //     })

        // })

        // function exportProgress() {

        //     $.ajax({
        //         method: "GET",
        //         dataType: 'json',
        //         url:  '/settings/charities/export-progress/' + batch_id,
        //         success: function(data)
        //         {
        //             if (data.finished) {
        //                 clearInterval(intervalID);
        //                 $('#export-btn').prop('disabled', false);
        //             }
        //             $('#export-section-result').html(data.message);
        //         },
        //         error: function(response) {
        //             if (response.status == 422) {
        //                 $('#export-btn').prop('disabled', false);
        //                 $('#export-section-result').html('');

        //                 Swal.fire({
        //                     title: 'Export failed!',
        //                     text: response.responseJSON.message,
        //                     icon: 'error',
        //                 })
        //                 clearInterval(intervalID);
        //             }
        //             console.log('export job error');
        //         }
                
        //     });

        // }
           
            

        // // Model for creating new charity
        // $('#charity-create-modal').on('show.bs.modal', function (e) {
        //     // do something...
        //     var fields = ['code', 'name', 'status', 'effdt', 'notes'];
        //     $.each( fields, function( index, field_name ) {
        //         $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
        //         $(document).find('[name='+field_name+']').val('');
        //     });
        //     $('#charity-create-modal').find('[name=status]').val('A');

        // })

        // $(document).on("click", "#create-confirm-btn" , function(e) {
		
        //     var form = $('#charity-create-model-form');
        //     var id = e.target.value;
            
        //     info = 'Are you sure to create this record?';
        //     if (confirm(info))
        //     {
                    
        //         var fields = ['code', 'name', 'status', 'effdt', 'notes'];
        //         $.each( fields, function( index, field_name ) {
        //             $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
        //         });

        //         $.ajax({
        //             method: "POST",
        //             url:  '{{ route('settings.charities.store')  }}',
        //             data: form.serialize(), // serializes the form's elements.
        //             success: function(data)
        //             {
        //                 oTable.ajax.reload(null, false);	// reload datatables
        //                 $('#charity-create-modal').modal('hide');
                        
        //                 var code = $("#charity-create-model-form [name='code']").val();
        //                 Toast('Success', 'Region code ' + code +  ' was successfully created.', 'bg-success' );
        //             },
        //             error: function(response) {
        //                 if (response.status == 422) {
                            
        //                     $.each(response.responseJSON.errors, function(field_name,error){
        //                         $(document).find('#charity-create-model-form [name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
        //                     })
        //                 }
        //                 console.log('Error');
        //             }
        //         });
            
        //     };
        // });

        // Model -- Edit 
    	$(document).on("click", ".edit-charity" , function(e) {
			e.preventDefault();

            // clean up old error messages
            var fields = ['alt_address1', 'alt_address2', 'alt_city', 'alt_province', 'alt_postal_code', 
                        'alt_country', 'financial_contact_name', 'financial_contact_title',
                        'financial_contact_email', 'comments'];
            $.each( fields, function( index, field_name ) {
                $('#charity-edit-model-form [name='+field_name+']').nextAll('span.text-danger').remove();
            });

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

         $(document).on("click", "#save-confirm-btn" , function(e) {
		
            var form = $('#charity-edit-model-form');
            var id = $("#charity-edit-model-form [name='id']").val();
            
            info = 'Confirm to update this record?';
            if (confirm(info))
            {
                var fields = ['alt_address1', 'alt_address2', 'alt_city', 'alt_province', 'alt_postal_code', 
                            'alt_country', 'financial_contact_name', 'financial_contact_title',
                            'financial_contact_email', 'comments'];
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
                            // console.log(field_value);
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
