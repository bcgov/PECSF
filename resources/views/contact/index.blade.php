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

                <button style="cursor:pointer" onclick="toggle();" id="toggle_all_hook" class="btn-primary  btn-sm btn float-right">
Expand All
                </button>
            </h2>
<div style="clear:both;"></div>
            <section>
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
                            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;"  data-target="#collapse{{$i}}{{$section}}" aria-expanded="{{$i==0 ? 'false' : 'false'}}" aria-controls="collapse{{$i}}{{$section}}">
                                <button class="btn btn-link">
                                    {{$qna['question']}}
                                </button>
                                <div class="flex-fill"></div>
                                <div class="expander">

                                </div>
                            </h5>
                        </div>

                        <div id="collapse{{$i}}{{$section}}" class="collapse {{$i==0 ? '' : ''}}" aria-labelledby="heading{{$i}}{{$section}}" data-parent="#accordion{{$section}}">
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
@push('js')
    <script>

        var toggleCount = $(".expander").length;

        function toggle(){
            if($("#toggle_all_hook").text() == "Collapse All"){
                $(".card-header h2").attr("aria-expanded",false);
                $(".card-header h5").attr("aria-expanded",false);
                $(".collapse").hide();
                $("#toggle_all_hook").text("Expand All");
                $("#toggle_all_hook").removeClass("btn-secondary").addClass("btn-primary");


            }
            else if($("#toggle_all_hook").text() == "Expand All"){
                $(".card-header h2").attr("aria-expanded",true);
                $(".card-header h5").attr("aria-expanded",true);
                $(".collapse").show();
                $("#toggle_all_hook").text("Collapse All");
                $("#toggle_all_hook").removeClass("btn-primary").addClass("btn-secondary");

            }
        }


        $(".card-header").click(function(){
            $($(this).children("h5").attr("data-target")).toggle();
            $(this).children("h5").attr("aria-expanded",$(this).children("h5").attr("aria-expanded") == "true" ? "false" : "true");
            $(this).children("h2").attr("aria-expanded",$(this).children("h2").attr("aria-expanded") == "true" ? "false" : "true");
            open = $("[aria-expanded='true']").length;
            closed = toggleCount - open;
            if(open == toggleCount){
                $("#toggle_all_hook").text("Collapse All");

                $("#toggle_all_hook").removeClass("btn-primary").addClass("btn-secondary");

            }
            if(closed == toggleCount){
                $("#toggle_all_hook").text("Expand All");

                $("#toggle_all_hook").removeClass("btn-secondary").addClass("btn-primary");
            }
        });
    </script>
@endpush
@endsection
