@extends('adminlte::page')

@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('admin-volunteering.partials.tabs')

    <h4 class="mx-1 mt-3">Upload Volunteer Profiles</h4>
    
    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>    
        </div>

        <div class="d-flex">
            <div class="mr-2">
            </div>
        </div>
    </div>

@endsection
@section('content')

        {{-- @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span>{{ $message }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif --}}

    <h6>Select the relevant organization, upload PECSF Donation files for non BC Gov entities below, then click "Submit" to send reports to PECSF administration.</h6>
    <div class="card">
        <div class="card-body">

            <form id="upload-form" action="{{ route("admin-volunteering.upload-profile.store") }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <h6 class="text-primary font-weight-bold pb-2">Upload Volunteer Profile Files</h6>

                <div class="form-group col-md-12">
                    <fieldset class="form-group row">
                        <label class="col-md-2" for="campaign_year">
                            Campaign Year
                        </label>
                        <div class="col-sm-1">
                            <select id="campaign_year" class="form-control" name="campaign_year">
                                <option value="all">All</option>
                                @foreach ($year_list as $year)
                                    <option value="{{ $year }}" {{ 
                                        isset($filter['campaign_year']) ? ($filter['campaign_year'] == $year ? 'selected' : '') :
                                        ($year == today()->year - 1  ? 'selected' : '') }}>
                                        {{ $year }} 
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                </div> 

                <div class="form-group col-md-12">
                    <fieldset class="form-group row">
                        <label class="col-md-2" for="campaign_year">
                            Type of Organization
                        </label>
                        {{-- <legend class="col-form-label col-sm-2 float-sm-left pt-0"></legend> --}}
                        <div class="col-sm-10">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="org_type" id="gridRadios1" value="1" checked>
                                <label class="form-check-label" for="gridRadios1">
                                    Government 
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="org_type" id="gridRadios2" value="2">
                                <label class="form-check-label" for="gridRadios2">
                                    Non-Government (e.g. LDB, LA etc)
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </div>

                {{-- <div class="form-group">
                        <label for="user_id">Organization</label>

                        <select class="form-control col-4" name="organization_id" id="organization_id">
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->name }}</option>
                            @endforeach
                        </select>
                </div> --}}

                {{-- <div class="form-group">
                    <label class="btn btn-primary" for="charity_list"><img style="width:16px;height:16px;" src="{{asset('img/icons/upload.png')}}"/>&nbsp;Upload File</label>
                    <input type="file" style="display:none;" accept=".txt" id="charity_list" name="charity_list" />
                </div> --}}

                <div class="form-row">
                    <div class="form-group col-md-10">
                        <div class="file-upload">
                            <div class="file-select">
                                <div class="file-select-button" id="fileName">Choose File</div>
                                <div class="file-select-name" id="noFile">No file chosen...</div>
                                <input type="file" accept=".xlsx,.xls" name="donation_file" id="donation_file">
                            </div>
                        </div>
                        <span class="donation_file_error">
                            @error( 'donation_file' )
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </span>
                    </div>

                    <div class="col-md-1" id="remove-upload-area" style="display: none;">
                        <div class="pt-1"><button id="remove-upload-file" class="btn btn-danger">
                            <i class="fas fa-trash-alt fa-lg"></i></button></div>
                    </div>

                </div>

                <div class="form-row pt-3">
                    <div class="form-group col-md-6 float-right">
                        <input class="btn btn-outline-secondary" id="cancel-btn" type="button" value="Cancel">
                        <input class="btn btn-primary " type="submit" value="Submit">
                    </div>
                </div>

            </form>

        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-outline-primary" id="refresh-btn">Refresh</button>
            </div>

        </div>
        <div class="card-body">

            <table class="table table-bordered" id="history-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Process ID</th>
                        <th>File Name</th>
                        <th>Submitted At</th>
                        <th>Start At</th>
                        <th>End At</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Message</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>

{{-- Modal Box  --}}
<div class="modal fade" id="process-show-modal" tabindex="-1" role="dialog" aria-labelledby="processModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="processModalLabel">Existing details</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">


                <p class="px-2"><b>Status : </b>
                <span id="modal-status"></span>
                </p>


            <div class="form-group px-2">
                <label for="message">Log Message</label>
                <pre id="modal-message" class="border" style="white-space:pre-line;"></pre>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>

    </div>
</div>
</div>
@endsection



@push('css')

    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">

    <style>

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

        .dataTables_scroll {
            padding-bottom : 20px;
        }

        div.dataTables_wrapper div.dataTables_processing {
         top: 5%;
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

    <script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}" ></script>
    <script>

    $(function() {

        var oTable = $('#history-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },            
            serverSide: true,
            // select: true,
            'order': [[0, 'desc']],
            "initComplete": function(settings, json) {
                oTable.columns.adjust().draw();
            },
            ajax: {
                url: '{!! route('admin-volunteering.upload-profile.index') !!}',
                data: function (d) {
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
                {data: 'id', className: "dt-nowrap"},
                {data: 'original_filename', width: '80px'},
                {data: 'submitted_at',  className: "dt-nowrap"},
                {data: 'start_at', defaultContent: '', className: "dt-nowrap" },
                {data: 'end_at', defaultContent: '', className: "dt-nowrap"},
                {data: 'status', "className": "dt-center"},
                {data: 'action'},
                {data: 'message_text', className: "dt-nowrap"},
            ],
            columnDefs: [
                    {

                    },
            ]
        });


        $("#refresh-btn").click(function() {
            oTable.ajax.reload( null, false );
        })


        // Model -- Show
    	$(document).on("click", ".more-link , .show-process" , function(e) {
			e.preventDefault();

            id =  $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '{!! route('admin-volunteering.upload-profile.index') !!}/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $('#processModalLabel').html('Process : ' + data.id + ' (' + data.process_name + ')' );
                    //  started at ' + data.start_time);
                    $('#modal-status').html(data.status);
                    $('#modal-message').html(data.message);
                    $('#process-show-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});


        // Functions for handling the upload file
        $('#donation_file').bind('change', function () {
            var filename = $("#donation_file").val();
            if (/^\s*$/.test(filename)) {
                $(".file-upload").removeClass('active');
                $("#noFile").text("No file chosen...");

                $('.donation_file_error').html();
            }
            else {
                $(".file-upload").addClass('active');
                $("#noFile").text(filename.replace("C:\\fakepath\\", ""));

                $('#remove-upload-area').show();
            }
        });

        $(document).on("click", "#remove-upload-file, #cancel-btn" , function(e) {
            e.preventDefault();
            $("input[name='donation_file']").val(null);
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen...");
            $('#remove-upload-area').hide();

        });

        // Format Submission (Ajax)
        var isLoading = false;

        $("#upload-form").submit(function(e) {
            e.preventDefault();

            if (!isLoading) {
                isLoading = true;

                var form = document.getElementById("upload-form");
                var formData = new FormData();
                $("select[name='campaign_year']").each(function(){
                    formData.append('campaign_year', $(this).val());
                });
                $("input[name='org_type']:checked").each(function(){
                    formData.append('org_type', $(this).val());
                });
                $("input[name='donation_file']").each(function(){
                    if ($(this).val() ) {
                        formData.append('donation_file',  $(this)[0].files[0]);
                    }
                });

                var fields = ['organization_id', 'donation_file'];
                $.each( fields, function( index, field_name ) {
                    $('#upload-form [name='+field_name+']').nextAll('span.text-danger').remove();
                });
                $('.donation_file_error').html('');

                $("#upload-form").fadeTo("slow",0.2);
                $.ajax({
                    url: "{{ route('admin-volunteering.upload-profile.store') }}",
                    type:"POST",
                    data: formData,
                    headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
                    processData: false,     // tell jQuery not to process the data
                    contentType: false,     // tell jQuery not to set contentType
                    cache: false,
                    dataType: 'json',
                    success:function(response){

                        // Clear up the uploded file
                        //$("input[name='donation_file']").val('');
                        $("input[name='donation_file']").val(null);
                        $(".file-upload").removeClass('active');
                        $("#noFile").text("No file chosen...");
                        $('#remove-upload-area').hide();

                        oTable.ajax.reload(null, false);	// reload datatables

                        // var code = $("#bu-edit-model-form [name='code']").val();
                        // Toast('Success', response.success,  'bg-success');
                        toastr["success"]( response.success, '',
                                 {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });

                        // window.location = response[0];
                        console.log(response);

                    },
                    error: function(response) {
                        if (response.status == 422) {
                            $.each(response.responseJSON.errors, function(field_name,error){

                                if (field_name == 'donation_file') {
                                    $('.donation_file_error').html( '<span class="text-strong text-danger">' + error + '</span>');
                                } else {
                                    $(document).find('[name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                                }
                            })
                        }
                        console.log('Error');
                    },
                    complete:function(){
                        $("#upload-form").fadeTo("slow",1.0);
                        isLoading = false;
                    }
                    
                });

            }
            
        });

    });

    </script>
@endpush
