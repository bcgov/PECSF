@extends('adminlte::page')
@section('content_header')

@section('content')
    <div class="container landing-page" style="min-height: 1200px">
        <div class="row">
            <div class="col text-center">
                <h1 class="text-primary">Welcome, PECSF Administrator</h1>
                <p>Choose from the options below:</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                    <div class="card" >
                        <a href="{{ route('admin-pledge.campaign.index') }}">
                        <div class="card-body mt-4 text-center">
                            <div>
                                <img src="{{asset('img/admin/2.png')}}" style="height:100px">
                            </div>
                            Pledge Administration <br>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        </a>   
                    </div>
            </div>

            <div class="col">                
                <div class="card">    
                    <a href="{{ route('settings.campaignyears.index') }}">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/1.png')}}" style="height:100px">
                        </div>
                        Campaign Set-up<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    </a>    
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">                
                <div class="card">
                    <a href="{{ route('admin-volunteering.profile.index') }}">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/4.png')}}" style="height:100px">
                        </div>
                        {{-- Training, Communications and Engagement<br> --}}
                        Volunteering<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    </a>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <a href="{{ route('reporting.donation-upload.index') }}">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/3.png')}}" style="height:100px">
                        </div>
                        Reporting<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    </a>  
                </div>  
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>

    .landing-page .card:focus-within {
        /* padding: 4px 12px; */
        /* border: #000 2px solid !important; */
        outline: 4px solid #3b99fc;
        outline-offset: 1px;
    }

    .landing-page a:focus {
        /* padding: 4px 12px; */
        border: none;
        outline: none;
    }

</style>
@endpush

@push('js')
<script>

    $(function() {

         // prevent spacebar to trigger the page scrolling
        $(document).on("keypress", function(e) {
            var $focusElem = $(":focus");
            if (e.which == 32 && !($focusElem.is("input") || $focusElem.attr("contenteditable") == "true"))
                e.preventDefault();
        });

        // Enter or space key on Wizard STEP icon to forward and backward 
        $('.landing-page a').on('keyup', function(e) {
            // Enter or space key on Wizard STEP icon to forward and backward    
            var key  = e.key;
            if (key == ' ' || key == 'Enter') {
                e.preventDefault();
                console.log(this);
                link = $(this).attr('href');
                window.location.href = link;
                // $(this).click();
            }
        });

    });

</script>
@endpush
