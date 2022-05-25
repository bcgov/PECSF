@extends('adminlte::page')
@section('content_header')
    <div class="text-center mt-3">
        <h1>Contact PECSF</h1>
        <p class="px-5 mt-2">
            <b>Got a question? We're here to help! If you don't see your question answered in the FAQ section below, send us an e-mail at <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>.</b>
        </p>
    </div>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>FAQ</h2>
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
                            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse{{$i}}{{$section}}" aria-expanded="{{$i==0 ? 'false' : 'false'}}" aria-controls="collapse{{$i}}{{$section}}">
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
@endsection