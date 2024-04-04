@extends('adminlte::page')
@section('content_header')

<div class="mt-2">
    <h1>Challenge</h1>

    @include('challenge.partials.tabs')

    <h6 class="mt-3">View and download organization participation tracker</h6>
</div>

@endsection


@section('content')

<div class="card">
    <div class="card-body">

        @if (session('message'))
            <div class="alert alert-danger">
                {{ session('message') }}
            </div>
        @endif

        <form id="download_report" method="GET" enctype="multipart/form-data" action="{{ route("challenge.org_participation_tracker_download") }}" >
            @csrf

            <div class="form-row">
                <div class="form-group col-md-2">

                    <label for="campaign_year">
                        Campaign Year
                    </label>
                    <select id="sort" class="select form-control" name="campaign_year">
                        {{-- <option value="">Select Campaign Year</option> --}}
                        @foreach($year_options as $year) 
                            <option value="{{ $year - 1  }}" {{ ($year - 1) == today()->year ? 'selected' : '' }}>{{ $year - 1 }}</option>
                        @endforeach 
                    </select>
                </div>

                <div class="form-group col-md-4">

                    <label for="business_unit">
                        Organization
                    </label>
                    <select id="sort" class="select form-control" name="business_unit">
                        @foreach($business_units as $bu) 
                            <option value="{{ $bu->business_unit_code }}"  {{ $bu->business_unit_code == $default_bu ? 'select' : '' }}>{{ $bu->business_unit_name }}</option>
                        @endforeach 
                    </select>
                </div>

                
                <div class="form-group col-md-1">
                    <input type="hidden" name="excel" value = '1' />

                    <input type="submit" style="margin-top:30px;" class="btn btn-primary" value="Download Report" />
                </div>
            </div>
        </form>

        <div class="form-row">
            <div id="preview" class="form-group col-md-12">

            </div>
        </div>

    </div>
</div>


@endsection

@push('js')

<script>

$(function() {

    // $(document).on('change', 'select[name="sort"]', function (e) {
    //     // oTable.ajax.reload();
    //     if ((this.value) == 'department') {
    //         $('select[name="start_date"] option[final="1"]').hide();
    //         $('select[name="start_date"] option[final="2"]').show();

    //         choice = '';    // Always reset to blank

    //     } else {
    //         $('select[name="start_date"] option[final="1"]').show();
    //         $('select[name="start_date"] option[final="2"]').hide();

    //         choice = $('select[name="start_date"]').find('option:checked').val();
    //     }

    //     $('#start_date').val( choice ).change();
    // });

    // $( 'select[name="sort"]' ).trigger( "change" );

});            

</script>

@endpush