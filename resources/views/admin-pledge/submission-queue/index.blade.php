<div class="modal fade" id="edit-event-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-charity-name" id="charity-modal-label">Submission Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- content will be load here -->
                <div id="edit-event-modal-body">
                    @include('volunteering.partials.form')
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@extends('adminlte::page')

@section('content_header')


    <div class="d-flex mt-3">
        <h4>Pledge Administration</h4>
        <div class="flex-fill"></div>
    </div>
    <br>
    <br>
    @include('admin-pledge.partials.tabs')

@endsection
@section('content')


    <p><a href="/administrators/dashboard">Back</a></p>

    <p>
        Enter any information you have and click Search. Leave fields blank for a list of all values.
    </p>



    @include('admin-pledge.partials.menu')

    <div style="clear:both;float:none;"></div>
    <br>
    <br>
    <div class="card">
        <div class="card-body">
            <h3 class="blue">PECSF Event Submissions Queue</h3>

            <table class="table">
                <thead>
                <tr>
                    <th>Donation Type</th>
                    <th>Employee Id</th>
                    <th>Name</th>
                    <th>Deposit Amount</th>
                    <th>Business Unit</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($submissions as $key => $submission)
                    @include('admin-pledge.submission-queue.partials.result_row')
                @endforeach
                </tbody>
            </table>


        </div>
    </div>



@endsection
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #campaign-table_filter label {
            text-align: right !important;
            padding-right: 10px;
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

        table tr{
            background:#fff;
        }
    </style>

@endpush
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="x-tmpl" id="organization-tmpl">
        @include('volunteering.partials.add-organization', ['index' => 'XXX', 'charity' => "YYY"] )
    </script>

    <script type="x-tmpl" id="attachment-tmpl">
        @include('volunteering.partials.add-attachment', ['index' => 'XXX'] )
    </script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).on("click", ".edit-event-modal" , function(e) {
            e.preventDefault();
            var row_number = 0;
            $.get("/admin-pledge/details",
                {
                    form_id: $(this).attr("form-id")
                },
                function (data, status) {
                    $('#organizations').html("");

                    if(data[0].charities.length > 0){

                        $("#organizations").show();
                        $(".org_hook").show();
                        $("#add_row").show();
                        $(".form-pool").hide();
                        $("input[value='dc']").attr("checked","checked");

                        for(i=0;i<data[0].charities.length;i++){
                            text = $("#organization-tmpl").html();
                            text = text.replace(/XXX/g, row_number + 1);
                            $('#organizations').append( text );
                            $("#organizations").css("display","block");
                            row_number++;
                            $('.organization').last().find(".organization_name").val(data[0].charities[i].organization_name);
                            $('.organization').last().find("[name='id[]']").val(data[0].charities[i].vendor_id);
                            $('.organization').last().find("[name='vendor_id[]']").val(data[0].charities[i].vendor_id);
                            $('.organization').last().find("[name='donation_percent[]']").val(data[0].charities[i].donation_percent);
                            $(".next_button").attr("disabled",false);
                        }
                    }


                    let attachment_number = 1;
                    $(".upload-area").hide();

                    for(i=0;i<data[0].attachments.length;i++) {
                        text = $("#attachment-tmpl").html();
                        text = text.replace(/XXX/g, attachment_number + 1);
                        $('#attachments').append( text );
                        attachment_number++;
                        $('.attachment').last().find(".filename").html(data[0].attachments[i].local_path.substring(data[0].attachments[i].local_path.indexOf("/"),data[0].attachments[i].local_path.length));
                        $('.attachment').last().find(".view_attachment").attr("href","/bank_deposit_form_attachments"+data[0].attachments[i].local_path.substring(data[0].attachments[i].local_path.indexOf("/"),data[0].attachments[i].local_path.length));
                    }

                    $('#edit-event-modal').modal('show');
                    console.log(data);
                },"json");
        });
        $("select").select2();
    </script>
    @include('volunteering.partials.add-event-js')

@endpush
