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
        <form>
            <div class="form-row">
            <div class="form-group col-md-3">
                <label for="organization_code">
                    Organization Code
                </label>
                <input name="organization_code" id="organization_code" class="form-control" />
                <span class="organization_code_errors errors">
                       @error('organization_code')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>

            <div class="form-group col-md-3">
                <label for="employee_id">
                    Employee ID
                </label>
                <input name="employee_id" id="employee_id" class="form-control" />
                <span class="employee_id_errors errors">
                       @error('employee_id')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>

            <div class="form-group col-md-3">
                <label for="pecsf_id">
                    PECSF Identifier
                </label>
                <input name="pecsf_id" id="pecsf_id" class="form-control" />
                <span class="pecsf_id_errors errors">
                       @error('pecsf_id')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>

            <div class="form-group col-md-3">
                <label for="calendar_year">
                    Calendar Year
                </label>
                <select name="calendar_year" id="calendar_year" class="form-control">
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                </select>
                <span class="calendar_year_errors errors">
                       @error('calendar_year_id')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>
            </div>
            <input type="submit" style="background:#1A5A96;color:#fff;" value="Search" class="form-control" />
        </form>
@endsection
@push('css')
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
	<style>
	#campaign-table_filter label {
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
    <script>

        $(document).on("click", ".add-event-modal" , function(e) {
            e.preventDefault();
            $('#add-event-modal').modal('show');
        });

    </script>
@endpush
