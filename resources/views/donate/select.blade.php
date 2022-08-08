@extends('donate.layout.main')

@section ("step-content")
    <div style="position:relative;top:-400px;">
<h2 class="mt-5">2. Choose your charities (up to 10)</h2>
        <form action="{{route('donate.save.select')}}" method="post">
<div class=" form-row">
    @include('donate.partials.choose-charity')
        @csrf
</div>
            <div class="mt-2">
                <button class="btn btn-lg btn-outline-primary">Cancel</button>
                <button class="btn btn-lg btn-primary" type="submit">Next</button>
            </div>
        </form>
    </div>
@endsection
@push('css')
{{--
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.1.1/dist/select2-bootstrap-5-theme.min.css" />
--}}
<style>
    #selected-charity-list {
        min-height: 200px;
    }
</style>
@endpush
@push('js')
    @include('donate.partials.choose-charity-js')
    <script type="x-tmpl" id="organization-tmpl">
        @include('volunteering.partials.add-organization', ['index' => 'XXX'] )
    </script>
    <script>
        $(".org_hook").show();
    </script>
@endpush
