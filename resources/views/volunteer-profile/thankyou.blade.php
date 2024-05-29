@extends('adminlte::page')

@section('content_header')

@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            {{-- <img src="{{asset('img/thank-you.png')}}" alt="" class="p-3">
            <h1 class=" mt-5">Thank you</h1>
            <p class="text-muted"></p>
            <p class="mt-5 font-weight-bold text-center"><b>Thank you, {{Auth::user()->name}} for your generous donation to PECSF. <br>
            Every contribution makes a huge impact in your community</b></p>
            <p class=' mt-5'>Please Note all PECSF payroll deductions will automatically show on your T4 received each spring. <br> Should you require an additional copy, click the button below.</p>
            <a class="btn btn-primary btn-md mb-5" href="{{route('donate-now.summary',$pledge_id)}}?download_pdf=true">Download Donation Summary</a>
            <a class="btn btn-outline-primary mb-5 btn-md" href="{{ route('donations.list')}}">View Donation History</a>
            <br> --}}

                <h1>Registration Complete</h1>
                <div class="row mt-3">
                    <div class="col-12 col-md-6 offset-md-3">
                        <div class="step-1 text-center">
                            <p class="text-muted">
                                You have successfully registered as a volunteer with PECSF. <br>
                                Click the button below to learn more about available training. 
                            </p>
                            <div class="m-3">
                                <img src="{{asset('img/volunteering-intro/finished-registraion.jpeg')}}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3">
                    {{-- <button href="/training" role="button" class="btn ">Begin Volunteer Training</button> --}}
                    {{-- <button type="button" class="btn btn-primary btn-lg" onclick="location.href='/volunteering/profile/{{ $profile->id }}';"> --}}
                    <button type="button" class="btn btn-primary btn-lg" onclick="location.href='/volunteering';">
                        Back to Volunteering
                    </button>
                </div>

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
