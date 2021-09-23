@extends('donate.layout.main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <img src="{{asset('img/thank-you.png')}}" alt="" class="p-3">
            <h1 class=" mt-5">Thank you</h1>
            <p class="text-muted"></p>
<p class="mt-5 font-weight-bold text-center"><b>Thank you, Employee A for your generous donation to PECSF. <br>
Every contribution makes a huge impact in your community</b></p>
<a class="btn btn-primary mt-5 mb-5" href="mailto:">Email Donation Summary</a>
<a class="btn btn-outline-primary mt-5 mb-5">View Donation History</a>
<br>
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