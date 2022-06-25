<div class="modal fade" id="add-event-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-charity-name" id="charity-modal-label">Add a New Value</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- content will be load here -->
                <div id="add-event-modal-body">

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

<div class="button-group">
    <div class="active">Find an Existing Value</div>
    <div class="add-event-modal">Add a New Value</div>
    <div>PECSF Event Submission Queue</div>
</div>
<div style="clear:both;float:none;"></div>
<br>
<br>
<div class="card">
	<div class="card-body">
        <h3>Search Criteria</h3>
        <form method="GET" action="{{route('admin-pledge.maintain-event.index')}}">
            <div class="form-row">
            <div class="form-group col-md-6">
                <label for="search_by">
                    Search By
                </label>
                <select name="search_by" id="search_by" class="form-control">
                    <option value="calendar_year" {{$request->search_by == 'calendar_year' ? 'selected' : ''}}>Calendar Year</option>
                    <option value="organization_code" {{$request->search_by == 'organization_code' ? 'selected' : ''}}>Organization Code</option>
                    <option value="employee_id" {{$request->search_by == 'employee_id' ? 'selected' : ''}}>Employee ID</option>
                    <option value="id" {{$request->search_by == 'id' ? 'selected' : ''}}>PECSF Identifier</option>
                </select>
                <span class="search_by_errors errors">
                       @error('search_by')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>

            <div class="form-group col-md-6">
                <label for="begins_with">
                    Begins With
                </label>
                <input name="begins_with" id="begins_with" value="{{$request->begins_with}}" class="form-control" />
                <span class="begins_with_errors errors">
                       @error('begins_with')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>
            </div>

            <div class="form-row">

            <div class="form-group col-md-12">
                <label for="limit">
                    Limit the number of results to (up to 300)
                </label>
                <input name="limit" id="limit" value="{{$request->limit ? $request->limit : 30}}" class="form-control" />
                <span class="limit_errors errors">
                       @error('limit')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="submit" style="background:#1A5A96;color:#fff;" value="Search" class="form-control" />
                </div>
                <div class="form-group col-md-6">
                   <span class="blue">Advanced Search</span>
                </div>
            </div>
        </form>


        <h3 class="blue">Search Results</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Organization Code</th>
                <th>Employee ID</th>
                <th>PECSF Identifier</th>
                <th>Calendar Year</th>
                <th>Deposit Date</th>
                <th>Event Type</th>
                <th>Sub Type</th>
                <th>Deposit Amount</th>
                <th>Description</th>
                <th>Employment City</th>
            </tr>
            </thead>
            <tbody>
                @foreach($event_pledges as $key => $pledge)
                    @include('admin-pledge.event.partials.result_row')
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
                @include('volunteering.partials.add-organization', ['index' => 'XXX'] )
            </script>

            <script type="x-tmpl" id="attachment-tmpl">
                @include('volunteering.partials.add-attachment', ['index' => 'XXX'] )
            </script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).on("click", ".add-event-modal" , function(e) {
            e.preventDefault();
            $('#add-event-modal').modal('show');
        });
        $("select").select2();
        @include('volunteering.partials.add-event-js')
    </script>
@endpush
