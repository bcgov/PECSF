@extends('adminlte::page')
@section('content_header')
    <h2>Reporting</h2>
    @include('admin-report.partials.tabs')
    <div class="d-flex mt-3">
        <h4>PECSF - Donation Upload</h4>
        <div class="flex-fill"></div>
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
    
            <form id="upload-form" action="{{ route("reporting.donation-upload.store") }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <h6 class="text-primary font-weight-bold">Upload Donation Files</h6>
                <div class="form-group">
                        <label for="user_id">Organization</label>

                        <select class="form-control col-4" name="organization_id" id="organization_id">
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->name }}</option>
                            @endforeach
                        </select>
                </div>

                {{-- <div class="form-group">
                    <label class="btn btn-primary" for="charity_list"><img style="width:16px;height:16px;" src="{{asset('img/icons/upload.png')}}"/>&nbsp;Upload File</label>
                    <input type="file" style="display:none;" accept=".txt" id="charity_list" name="charity_list" />
                </div> --}}

                <div class="form-group col-md-6">
                    

                    <div class="image">
                        <label>Attach file</label>
                        

                            <input id="donation_file" accept=".xlsx" type="file" class="form-control-file @error('donation_file') is-invalid @enderror"
                                    name="donation_file" value="{{ old('donation_file') }}">
                            {{-- <img style="width:auto;height:300px;" id="output" /> --}}
        
                        <span class="images_errors">
                            @error( 'donation_file' )
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </span>
                    </div>
                </div>

                
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="Submit">
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

@endsection



@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

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

    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script>

    $(function() {

        var oTable = $('#history-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            // select: true,
            'order': [[1, 'desc']],
            ajax: {
                url: '{!! route('reporting.donation-upload.index') !!}',
                data: function (d) {
                }
            },
            columns: [
                {data: 'original_filename',  className: "dt-nowrap"},
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
                url:  '{!! route('reporting.donation-upload.index') !!}/' + id,
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

        function Toast( toast_title, toast_body, toast_class) {
            $(document).Toasts('create', {
                            class: toast_class,
                            title: toast_title,
                            autohide: true,
                            delay: 12000,
                            body: toast_body
            });
        }

        $("#upload-form").submit(function(e) {
            console.log('Test');
            e.preventDefault();

            var form = document.getElementById("upload-form");
            var formData = new FormData();
            $("select[name='organization_id']").each(function(){
                formData.append('organization_id', $(this).val());
            });
            $("input[name='donation_file']").each(function(){
                formData.append('donation_file',  $(this)[0].files[0]);
            });

            var fields = ['organization_id', 'donation_file'];
                $.each( fields, function( index, field_name ) {
                    $('#upload-form [name='+field_name+']').nextAll('span.text-danger').remove();
                });

            $("#upload-form").fadeTo("slow",0.2);
            $.ajax({
                url: "{{ route('reporting.donation-upload.store') }}",
                type:"POST",
                data: formData,
                headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
                processData: false,     // tell jQuery not to process the data
                contentType: false,     // tell jQuery not to set contentType
                cache: false,
                dataType: 'json',
                success:function(response){

                    // Clear up the uploded file 
                    $("input[name='donation_file']").val(''); 

                    oTable.ajax.reload(null, false);	// reload datatables

                    // var code = $("#bu-edit-model-form [name='code']").val();
                    Toast('Success', response.success,  'bg-success');

                    // window.location = response[0];
                    console.log(response);
                  
                },
                error: function(response) {
                    if (response.status == 422) {
                        $.each(response.responseJSON.errors, function(field_name,error){
                            $(document).find('[name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                        })
                    }
                    console.log('Error');
                },
                complete:function(){ 
                    $("#upload-form").fadeTo("slow",1);
                }
            });



        });

    });

    </script>
@endpush
