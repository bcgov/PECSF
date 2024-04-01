@extends('adminlte::page')
@section('content_header')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="text-left mt-3">
                <h1>Contact PECSF</h1>
                <p>
                    Got a question? We're here to help! If you don't see your question answered in the FAQ section below, send us an e-mail at <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>.
                </p>
            </div>
         </div>
    </div>
</div>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 aria-expanded="false">FAQ

                <button style="cursor:pointer" onclick="toggle();" id="toggle_all_hook" class="btn-primary btn float-right">Expand All</button>
            </h2>
<div style="clear:both;"></div>
            <section id="accordion">
                @foreach($sections as $section => $qnas)
                <h5>
                @if($section == 'Canlendar')
                    PECSF program
                @else
                    {{$section}}
                @endif
                </h5>
                <div id="accordion{{$section}}">
                    @foreach($qnas as $i => $qna)
                    <div class="card">
                        <div class="card-header" id="heading{{$i}}{{$section}}">
                            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse{{$i}}{{$section}}" aria-expanded="{{$i==0 ? 'false' : 'false'}}" aria-controls="collapse{{$i}}{{$section}}">
                                {{-- <button class="btn btn-link"> --}}
                                    {{$qna['question']}}
                                {{-- </button> --}}
                                <div class="flex-fill"></div>
                                <button class="btn btn-link btn-nav-accordion" aria-label="Expand">
                                    <div class="expander">

                                    </div>
                                </button>
                            </h5>
                        </div>

                        <div id="collapse{{$i}}{{$section}}" class="collapse {{$i==0 ? '' : ''}}" aria-labelledby="heading{{$i}}{{$section}}" 
                                    {{-- data-parent="#accordion{{$section}}" --}} >
                            <div class="card-body">
                                {!! $qna['answer'] !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </section>
        </div>
    </div>
</div>

@push('css')
<style>

    .expander {
        border: 1px solid $primary;
        border-radius: 50%;
        background-color: $primary;
        color: #fff;
        width: 1.3em;
        height: 1.3em;
        text-align: center;
        font-size: 1.6em;;
        line-height: 1.25em;
        font-weight: bold;
        &::after {
            content: '+';
        }
    }

    [aria-expanded="true"] .expander::after{
        content: '-';
    }

</style>
@endpush

@push('js')
    <script>

        var toggleCount = $(".expander").length;

        function toggle(){
            if($("#toggle_all_hook").text() == "Collapse All"){
                $(".collapse").each(function() {
                    $( this ).collapse('hide');
                });

                $("#toggle_all_hook").text("Expand All");
                $("#toggle_all_hook").removeClass("btn-secondary").addClass("btn-primary");

            }
            else if($("#toggle_all_hook").text() == "Expand All"){
                $(".collapse").each(function() {
                    $( this ).collapse('show');
                });
                
                $("#toggle_all_hook").text("Collapse All");
                $("#toggle_all_hook").removeClass("btn-primary").addClass("btn-secondary");
            }

            $("#accordion .card-header h5").find('button.btn-nav-accordion').attr('aria-label', 'Expand'); 
            $("#accordion .card-header h5[aria-expanded='true']").find('button.btn-nav-accordion').attr('aria-label', 'Collapse');   
            
        }


        $(".card-header").click(function(){
            if ( $(this).children("h5").attr("aria-expanded") == "true") {
                $($(this).children("h5").attr("data-target")).collapse('hide');
            } else {
                $($(this).children("h5").attr("data-target")).collapse('show');
            }

            open = $("[aria-expanded='true']").length;
            closed = toggleCount - open;
            if(open == toggleCount){
                $("#toggle_all_hook").text("Collapse All");

                $("#toggle_all_hook").removeClass("btn-primary").addClass("btn-secondary");

            }
            if(toggleCount == (toggleCount - open)){
                $("#toggle_all_hook").text("Expand All");

                $("#toggle_all_hook").removeClass("btn-secondary").addClass("btn-primary");
            }
        });

    $(function() {

        $('#accordion').on('hidden.bs.collapse', function(event){
            $("#accordion .card-header h5").find('button.btn-nav-accordion').attr('aria-label', 'Expand'); 
            $("#accordion .card-header h5[aria-expanded='true']").find('button.btn-nav-accordion').attr('aria-label', 'Collapse');   
            
        });

        $('#accordion').on('shown.bs.collapse', function(event){
            $("#accordion .card-header h5").find('button.btn-nav-accordion').attr('aria-label', 'Expand'); 
            $("#accordion .card-header h5[aria-expanded='true']").find('button.btn-nav-accordion').attr('aria-label', 'Collapse');   

        });

    });
    </script>
@endpush
@endsection
