@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <div class="d-flex mt-3">
        <h2>Special Campaign</h2>
        <div class="flex-fill"></div>


        <div class="d-flex">
            <div class="mr-2">
                {{-- <x-button :href="route('settings.special-campaigns.create')">Add a New Value</x-button>         --}}
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bu-create-modal">
                    Add a New Value
                  </button>
            </div>
        </div>
    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard">Back</a></p>

<div class="card">
    <div class="card-body pb-0">
        <h2 class="text-primary">Search Criteria</h2>
        <p>Enter any informartion you have and click Search. Leave fields blank for a list of all values.</p>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="filter-keyword">
                    Keywords 
                </label>
                <input  id="filter-keyword"  class="form-control" />
            </div>

            <div class="form-group col-md-2">
                <label for="filter-year">
                    Calendar Year
                </label>
                <select id="filter-year" class="form-control">
                    <option value="">All</option>
                        @for ($year = 2020; $year <= 2049 ; $year++)
                            <option value="{{ $year }}">
                                {{ $year }} 
                            </option>
                        @endfor
                </select>
            </div>

            <div class="form-group col-md-3">
                <label for="filter-bn">
                    Business Registration Number
                </label>
                <input id="filter-bn"  class="form-control" />
            </div>

        </div>
        <div class="form-row">
           <div class="form-group col-md-1">
                <input type="button" id="filter-reset-btn" value="Clear" class="form-control btn btn-outline-primary" />
            </div>
            <div class="form-group col-md-1">
                <input type="button" id="filter-refresh-btn" value="Search" class="form-control btn btn-primary" />
            </div>

        </div>

    </div>    
</div>        

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
                    <th>Name</th>
					<th>Charity</th>
                    <th>Registration Number</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>                    
                    <th>Action</th>
				</tr>
			</thead>
		</table>

	</div>
</div>

@include('admin-campaign.special-campaigns.partials.model-create')
@include('admin-campaign.special-campaigns.partials.model-edit')
@include('admin-campaign.special-campaigns.partials.model-show')

@endsection


@push('css')


    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">


	<style>
	#bu-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}

    #bu-table_filter {
        display: none;
    }

    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

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

.file-upload {display:block;text-align:center;font-family: Helvetica, Arial, sans-serif;font-size: 12px;}
.file-upload .file-select{display:block;border: 2px solid #dce4ec;color: #34495e;cursor:pointer;height:40px;line-height:40px;text-align:left;background:#FFFFFF;overflow:hidden;position:relative;}
.file-upload .file-select .file-select-button{background:#dce4ec;padding:0 10px;display:inline-block;height:40px;line-height:40px;}
.file-upload .file-select .file-select-name{line-height:40px;display:inline-block;padding:0 10px;}
.file-upload .file-select:hover{border-color:#34495e;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload .file-select:hover .file-select-button{background:#34495e;color:#FFFFFF;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload.active .file-select{border-color:#3fa46a;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload.active .file-select .file-select-button{background:#3fa46a;color:#FFFFFF;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload .file-select input[type=file]{z-index:100;cursor:pointer;position:absolute;height:100%;width:100%;top:0;left:0;opacity:0;filter:alpha(opacity=0);}
.file-upload .file-select.file-select-disabled{opacity:0.65;}
.file-upload .file-select.file-select-disabled:hover{cursor:default;display:block;border: 2px solid #dce4ec;color: #34495e;cursor:pointer;height:40px;line-height:40px;margin-top:5px;text-align:left;background:#FFFFFF;overflow:hidden;position:relative;}
.file-upload .file-select.file-select-disabled:hover .file-select-button{background:#dce4ec;color:#666666;padding:0 10px;display:inline-block;height:40px;line-height:40px;}
.file-upload .file-select.file-select-disabled:hover .file-select-name{line-height:40px;display:inline-block;padding:0 10px;}


</style>
@endpush


@push('js')

    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

    $(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        function Toast( toast_title, toast_body, toast_class) {
            // $(document).Toasts('create', {
            //                 class: toast_class,
            //                 title: toast_title,
            //                 autohide: true,
            //                 delay: 8000,
            //                 body: toast_body
            // });
            Swal.fire({
                    position: 'top-end',
                    icon: (toast_class.includes("bg-success") ? 'success' : 'warning'),
                    title: toast_title,
                    text: toast_body,
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 8000
            })
        }

        // Datatables
        var oTable = $('#bu-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            select: true,
            'order': [[0, 'asc']],
            ajax: {
                url: '{!! route('settings.special-campaigns.index') !!}',
                data: function (data) {
                    data.term = $('#filter-keyword').val();
                    data.year = $('#filter-year').val();
                    data.bn   = $('#filter-bn').val();
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
                {data: 'name', name: 'name', className: "dt-nowrap" },
                {data: 'charity.charity_name', name: 'charities.charity_name'},
                {data: 'charity.registration_number', name: 'registration_number' },
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'start_date'},
                {data: 'status', name: 'status', orderable: false, searchable: false },
                {data: 'action', name: 'action', className: "dt-nowrap", orderable: false, searchable: false}
            ],
            columnDefs: [
                    {
                        // width: '5em',
                        // targets: [0]
                    },
            ]
        });

        // filter
        $('#filter-refresh-btn').on('click', function() {
            // oTable.ajax.reload(null, true);
            oTable.draw();
        });

        $('#filter-reset-btn').on('click', function() {
            $('#filter-keyword').val('');
            $('#filter-year').val('');
            $('#filter-bn').val('');

            oTable.search( '' ).columns().search( '' ).draw();
        });

        // Select2 for Create and Edit form 
        //function to initialize select2
        function initializeSelect2(selectElementObj) {

            modal_obj = $(selectElementObj).closest('.modal');

            selectElementObj.select2({
                placeholder: 'select charity',
                dropdownParent: $(modal_obj),
                allowClear: true,
                ajax: {
                    url: '/settings/special-campaigns/charities'
                    , dataType: 'json'
                    , delay: 250
                    , data: function(params) {
                        var query = {
                            'q': params.term
                        , }
                        return query;
                    }
                    , processResults: function(data) {
                        return {
                            results: data
                            };
                    }
                    , cache: false
                }
            });

            selectElementObj.on('select2:select', function (e) {
                // Do something
                var data = e.params.data;
                var current_form = $(e.target).parents('form');

                $(current_form).find("input.registration_number").val(data.bn);
            });

            selectElementObj.on('select2:clear', function (e) {
                // Do something
                var data = e.params.data;
                var current_form = $(e.target).parents('form');

                $(current_form).find("input.registration_number").val('');
            });

        }

        //onload: call the above function
        $("select[name='charity_id']").each(function() {
           initializeSelect2($(this));
        });



        // Model for creating new special campaign
        $('#bu-create-modal').on('show.bs.modal', function (e) {
            // do something...
            var fields = ['name', 'charity_id', 'registration_number', 'start_date', 'end_date', 'description', 'banner_text'];
            $.each( fields, function( index, field_name ) {
                $('#bu-create-modal').find('[name='+field_name+']').nextAll('span.text-danger').remove();
                $('#bu-create-modal').find('[name='+field_name+']').val('');
            });
            
            $('#bu-create-modal').find("[name='charity_id']").val(null).trigger('change');
            
            $('#bu-create-modal').find("input[name='logo_image_file']").val(null);
            $('#bu-create-modal').find("div.file-select-name").html('No file chosen...');
            $('#bu-create-modal').find('div.remove-upload-area').hide();
            $('#bu-create-modal').find("img.upload-logo-image").attr('src', null);
            $('#bu-create-modal').find("img.upload-logo-image").css('display', 'none');
            $('#bu-create-modal .logo-image-file-error').find('.text-danger').remove();
        })

        $(document).on("click", "#create-confirm-btn" , function(e) {

            var form = $('#bu-create-model-form');
            var formData = new FormData( document.getElementById("bu-create-model-form") );
            var id = e.target.value;

            Swal.fire( {
                title: 'Are you sure you want to create this special campaign ?',
                // text: 'This action cannot be undone.',
                icon: 'question',
                //showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Create',
                buttonsStyling: false,
                //confirmButtonClass: 'btn btn-danger',
                customClass: {
                	confirmButton: 'btn btn-primary', //insert class here
                    cancelButton: 'btn btn-secondary ml-2', //insert class here
                }
                //denyButtonText: `Don't save`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {

                    var fields = ['name', 'charity_id', 'registration_number', 'start_date', 'end_date', 'description', 'banner_text'];
                    $.each( fields, function( index, field_name ) {
                        $(document).find('[name='+field_name+']').nextAll('span.text-danger').remove();
                    });
                    $('.logo-image-file-error').find('.text-danger').remove();

                    $.ajax({
                        method: "POST",
                        url:  '{{ route('settings.special-campaigns.store')  }}',
                        //data: form.serialize(), // serializes the form's elements.
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data)
                        {
                            // oTable.ajax.reload(null, false);	// reload datatables
                            oTable.draw();   // reload datatables
                            $('#bu-create-modal').modal('hide');

                            var name = $("#bu-create-model-form [name='name']").val();
                            Toast('Success', 'Special Campaign "' + name +  '" was successfully created.', 'bg-success' );
                        },
                        error: function(response) {
                            if (response.status == 422) {

                                $.each(response.responseJSON.errors, function(field_name,error){
                                    if (field_name == 'charity_id') {
                                        $('#bu-create-model-form .charity_id_errors').after('<span class="text-strong text-danger">' +error+ '</span>');
                                    } else if (field_name == 'logo_image_file') {
                                        $('#bu-create-model-form .logo-image-file-error').html('<span class="text-strong text-danger">' +error+ '</span>');
                                    } else {
                                        $(document).find('#bu-create-model-form [name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>');
                                    }
                                })
                            }
                            console.log('Error');
                        }
                    });
                } else if (result.isDismissed) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })

        });

        // Model -- Edit
    	$(document).on("click", ".edit-bu" , function(e) {
			e.preventDefault();

            var fields = ['name', 'charity_id', 'registration_number', 'start_date', 'end_date', 'description', 'banner_text'];
            $.each( fields, function( index, field_name ) {
                $('#bu-edit-model-form').find('[name='+field_name+']').nextAll('span.text-danger').remove();
            });
            $('#bu-edit-model-form .logo-image-file-error').find('.text-danger').remove();

            id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url:  '/settings/special-campaigns/' + id  + '/edit',
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        if (field_name == 'charity_id') {

                            // Assign the value for Charity ID and Registration
                            var newOption = new Option(data.charity.charity_name + ' ('+data.charity.registration_number+')', data.charity.id, true, true);
                            // Append it to the select
                            $('#bu-edit-model-form').find('[name="charity_id"]').append(newOption).trigger('change');
                            $('#bu-edit-model-form .registration_number').val(data.charity.registration_number);

                        } else  {
                            $('#bu-edit-model-form [name='+field_name+']').val(field_value);
                        }
                    });

                    // Image file 

                    $("#bu-edit-model-form").find(".file-upload").addClass('active');
                    $("#bu-edit-model-form").find('div.file-select-name').text( data.image ); 
                    

                    $("#bu-edit-model-form").find('div.remove-upload-area').show();
                    $("#bu-edit-model-form").find(".upload-logo-image").attr('src', '{{asset("img/uploads/special_campaign")}}/'+data.image);
                    $("#bu-edit-model-form").find(".upload-logo-image").css("display","block");
                 

                    $('#bu-edit-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});


        $(document).on("click", "#save-confirm-btn" , function(e) {

            var form = $('#bu-edit-model-form');
            var formData = new FormData( document.getElementById("bu-edit-model-form") );
            formData.append('_method', 'PUT');
            var id = $("#bu-edit-model-form [name='id']").val();

            info = 'Confirm to update this record?';
            // if (confirm(info))
            Swal.fire( {
                title: 'Are you sure you want to update the special campaign "' + name + '" ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Save',
                buttonsStyling: false,
                //confirmButtonClass: 'btn btn-danger',
                customClass: {
                	confirmButton: 'btn btn-primary', //insert class here
                    cancelButton: 'btn btn-secondary ml-2', //insert class here
                }
                //denyButtonText: `Don't save`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    var fields = ['name', 'charity_id', 'registration_number', 'start_date', 'end_date', 'description', 'banner_text'];
                    $.each( fields, function( index, field_name ) {
                        $('#bu-edit-model-form [name='+field_name+']').nextAll('span.text-danger').remove();
                    });
                    $('.logo-image-file-error').find('.text-danger').remove();

                    $.ajax({
                        method: "POST",
                        url:  '/settings/special-campaigns/' + id,
                        data: formData,     // Must use formData for uploading the file // data: form.serialize(), 
                        processData: false,
                        contentType: false,
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            $('#bu-edit-modal').modal('hide');

                            var name = $("#bu-edit-model-form [name='name']").val();
                            Toast('Success', 'Special Campaign "' + name +  '"" was successfully updated.', 'bg-success' );

                        },
                        error: function(response) {
                            if (response.status == 422) {

                                $.each(response.responseJSON.errors, function(field_name,error){
                                    if (field_name == 'charity_id') {
                                        $('#bu-edit-model-form .charity_id_errors').after('<span class="text-strong text-danger">' +error+ '</span>');
                                    } else if (field_name == 'logo_image_file') {
                                        $('#bu-edit-model-form .logo-image-file-error').html('<span class="text-strong text-danger">' +error+ '</span>');
                                    } else {
                                        $(document).find('#bu-edit-model-form [name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>');
                                    }
                                })

                            }
                            console.log('Error');
                        }
                    });
                } else if (result.isDismissed) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })

        });

        // Model -- Show
    	$(document).on("click", ".show-bu" , function(e) {
			e.preventDefault();

            id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '/settings/special-campaigns/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $.each(data, function(field_name,field_value ){
                        // console.log(field_name);
                        $(document).find('#bu-show-model-form [name='+field_name+']').val(field_value);
                    });

                    // Charity 
                    $(document).find("#bu-show-model-form [name='charity_name']").val( data.charity.charity_name);
                    $(document).find("#bu-show-model-form [name='registration_number']").val( data.charity.registration_number);

                    $(document).find("#bu-show-model-form figure.logo_image img").attr('src', '{{asset("img/uploads/special_campaign")}}/'+data.image);
                    $(document).find("#bu-show-model-form figure.logo_image span.logo_image_filename").html( data.image );
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
            name = $(this).attr('data-name');

            Swal.fire( {
                title: 'Are you sure you want to delete special campaign "' + name + '" ?',
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
                        url:  '/settings/special-campaigns/' + id,
                        dataType: 'json',
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            Toast('Success', 'Special Campagin "' + name +  '" was successfully deleted.', 'bg-success' );
                        },
                        // error: function(response) {
                            error: function (data) {
                                Swal.fire({
                                        icon: 'error',
                                        title: data.responseJSON.title, // data.responseJSON.title,
                                        text: data.responseJSON.message,
                                });

                                console.log(data.responseJSON.message);
                        }
                    });
                } else if (result.isDismissed) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })
        });

        
        // Functions for handling the upload file 
        $("input[name='logo_image_file']").bind('change', function (e) {
            var current_form = $(e.target).parents('form');
            console.log( e.target + ' - form : ' + current_form.attr('id') );
            var files = e.target.files;
            var filename = $(e.target).val();
            if (/^\s*$/.test(filename)) {
                $(current_form).find(".file-upload").removeClass('active');

                $(current_form).find('div.file-select-name').text("No file chosen..."); 
                // $("#noFile").text("No file chosen..."); 
                $(current_form).find('.logo-image-file-error').html();
            }
            else {
                $(current_form).find(".file-upload").addClass('active');


                // $("#noFile").text(filename.replace("C:\\fakepath\\", "")); 
                $(current_form).find('div.file-select-name').text(filename.replace("C:\\fakepath\\", "")); 

                $(current_form).find('div.remove-upload-area').show();

                $(current_form).find(".upload-logo-image").attr('src', URL.createObjectURL( files[0] ));
                $(current_form).find(".upload-logo-image").css("display","block");
            }

            // blank out input[name='name'] 
            $(current_form).find("input[name='image']").val('');

        });
				

        $(document).on("click", ".remove-upload-file, #cancel-btn" , function(e) {
            e.stopPropagation();
            e.preventDefault();

            var current_form = $(e.target).parents('form');

            $(current_form).find("input[name='logo_image_file']").val(null);
            $(current_form).find(".file-upload").removeClass('active');

            $(current_form).find('div.file-select-name').text("No file chosen..."); 
            // $("#noFile").text("No file chosen..."); 

            $(current_form).find('div.remove-upload-area').hide();

            $(current_form).find(".upload-logo-image").attr('src', null);
            $(current_form).find(".upload-logo-image").css("display","none");

            // blank out input[name='name'] 
            $(current_form).find("input[name='image']").val('');
            

        });

    });
    </script>
@endpush
