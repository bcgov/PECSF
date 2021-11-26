@extends('adminlte::page')
@section('content_header')
    <div class="text-center mt-3">
        <h1>Contact PECSF</h1>
        <p class="px-5 mt-2">
            <b>Got a question? We're here to help! If you don't see your question answered in the FAQ section below, send us an e-mail at PECSF@gov.bc.ca</b>
        </p>
    </div>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>FAQ</h2>
            <section>
                @foreach($sections as $section)
                <h5>{{$section}}</h5>
                <div id="accordion{{$section}}">
                    @for ($i = 0; $i < 2; $i++)
                    <div class="card">
                        <div class="card-header" id="heading{{$i}}">
                            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse{{$i}}" aria-expanded="{{$i==0 ? 'true' : 'false'}}" aria-controls="collapse{{$i}}">
                                <button class="btn btn-link">
                                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Reiciendis sit itaque facere 
                                </button>
                                <div class="flex-fill"></div>
                                <div class="expander">
                                    
                                </div>
                            </h5>
                        </div>

                        <div id="collapse{{$i}}" class="collapse {{$i==0 ? 'show' : ''}}" aria-labelledby="heading{{$i}}" data-parent="#accordion{{$section}}">
                            <div class="card-body">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
                @endforeach
            </section>
        </div>
    </div>
</div>
@endsection