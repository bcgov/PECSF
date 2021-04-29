@extends('donate.layout.main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-sm-7 pt-5  text-center">
            <h1 class=" mt-5">Thank you</h1>
            <p class="text-muted"></p>
<p class="mt-5 font-weight-bold text-center"><b>A word from the PECSF team on behalf of charity organisations.</b></p>
<p class="mt-3 font-weight-bold text-center"><b>Maybe we can mention about the volunteering program and how to find out more.</b></p>
<a class="btn btn-primary mt-5 mb-5" href="mailto:">Email me a copy of the summary</a>
<br>
<a class="btn btn-primary mt-5" href="/">Done</a>
</div>
        <div class="col-12 col-sm-5">
            <img src="{{ asset('img/donor.png') }}" alt="Donor" class="py-5 img-fluid">
        </div>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.1.1/dist/select2-bootstrap-5-theme.min.css" />
<style>
 
</style>
@endpush
@push('js')

@endpush