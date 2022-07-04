@extends('adminlte::page')
@section('content_header')
    @include('admin-campaign.partials.tabs')
    <div class="d-flex mt-3">
        <h4>PECSF - Charity List Maintenance</h4>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
                <h5>Charity List Upload</h5>
            <form action="{{ route("settings.charity-list-maintenance.store") }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <label class="btn btn-primary" for="charity_list"><img style="width:16px;height:16px;" src="{{asset('img/icons/upload.png')}}"/>&nbsp;Upload File</label>
                <input type="file" style="display:none;" accept=".txt" id="charity_list" name="charity_list" />
            </form>
            <div id="charity_list_errors">
                @foreach ($errors->all() as $error)
                        <span class="invalid-feedback">{{ $error }}</span>
                    @endforeach


            </div>
            <table class="table table-bordered" id="region-table" style="width:100%">
                <thead>
                <tr>
                    <th>File Name</th>
                    <th>Last Modified</th>
                    <th>Attempts</th>
                    <th>Size</th>
                    <th>Status</th>
                </tr>
                @foreach ($jobs as $job)
                   <tr>
                       <td>{{ $job->getCommand()->file_name }}</td>
                       <td>{{ $job->getLastModified() }}</td>
                       <td>{{ $job->getFailedAttempts() }}</td>
                       <td>{{ $job->getCommand()->file_size }}</td>
                       <td>Pending</td>
                   </tr>
                @endforeach
                @foreach ($completed_jobs as $job)
                    <tr>
                        <td>{{ $job->getCommand()->file_name }}</td>
                        <td>{{ $job->getLastModified() }}</td>
                        <td>{{ $job->getFailedAttempts() }}</td>
                        <td>{{ $job->getCommand()->file_size }}</td>
                        <td>Completed</td>
                    </tr>
                @endforeach
                @foreach ($failed_jobs as $job)
                    <tr>
                        <td>{{ $job->getCommand()->file_name }}</td>
                        <td>{{ $job->getLastModified() }}</td>
                        <td>{{ $job->getFailedAttempts() }}</td>
                        <td>{{ $job->getCommand()->file_size }}</td>
                        <td>Failed</td>
                    </tr>
                @endforeach
                </thead>
            </table>
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


    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script>

        var charitiesTimer;
        $(".invalid-feedback").fadeTo("slow",1);
        function updateCharities(){
            $.ajax({
                method: "GET",
                url:  '{{ route('settings.charity-list-maintenance.index')  }}',
                data: $("#charity-search").serialize(), // serializes the form's elements.
                success: function(html)
                {
                    $("#charity-table-body").html(html);
                },
                error: function(html) {
                    $("#charity-table-body").html(html);
                    console.log('Error');
                }
            });
        }

        $("input[name='charity_list']").change(function(){
            $(this).parents("form").submit();

        });

        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });

$("input[name='organization_name'],input[name='business_number']").keyup(function(){
    clearTimeout(charitiesTimer);
    charitiesTimer = setTimeout(updateCharities,1500);

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
