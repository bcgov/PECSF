@extends('donate.layout.main')

@section ("step-content")
    <div style="">
<h2 class="mt-5">2. Choose your charities (up to 10)</h2>
        <form action="{{route('donate.save.select')}}" method="post">
<div class=" form-row">
    @include('donate.partials.choose-charity')
        @csrf
</div>
            <div class="mt-2">
                <a href="{{route('donate.start')}}"><div class="btn btn-lg BC-Gov-SecondaryButton">Back</div></a>
                <button class="next_button btn btn-lg btn-primary" disabled type="submit">Next</button>
            </div>
        </form>
    </div>
@endsection
@push('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.1.1/dist/select2-bootstrap-5-theme.min.css" />

<style>
    #selected-charity-list {
        min-height: 200px;
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


</style>
@endpush
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @include('donate.partials.choose-charity-js')
    <script type="x-tmpl" id="organization-tmpl">
        @include('volunteering.partials.add-organization', ['index' => 'XXX', 'charity' => 'YYY'] )
    </script>
    <script>
        $(".org_hook").show();
    </script>
@endpush
