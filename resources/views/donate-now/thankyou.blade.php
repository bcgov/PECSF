@extends('donate.layout.main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <img src="{{asset('img/thank-you.png')}}" alt="" class="p-3">
            <h1 class=" mt-5">Thank you</h1>
            <p class="text-muted"></p>
<p class="mt-5 font-weight-bold text-center"><b>Thank you, {{Auth::user()->name}} for your generous donation to PECSF. <br>
Every contribution makes a huge impact in your community</b></p>
<p class=' mt-5'>Please Note all PECSF payroll deductions will automatically show on your T4 received each spring. <br> Should you require an additional copy, click the button below.</p>
<a class="btn btn-primary mb-5" href="{{route('donate.summary')}}?download_pdf=true">Download Donation Summary</a>
<a class="btn btn-outline-primary mb-5" href="{{ route('donations.list')}}">View Donation History</a>
<br>
</div>

    </div>
</div>
@endsection
{{-- @push('css')
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.1.1/dist/select2-bootstrap-5-theme.min.css" />
<style>

</style>
@endpush
@push('js')

@endpush --}}
