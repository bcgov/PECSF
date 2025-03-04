@extends('donate.layout.main')

@section ("step-content")
<h3 class="mt-5">2. Select your regional charity pool (up to 10)</h3>
<div>
    {{-- <p class="p-1"></p> --}}
    {{-- <div class="card mx-3 pl-3 bg-primary">
        <div class="card-body bg-light">
          If you select the option to choose from the CRA Charity List, you can select up to 10 difference
          charities of your choice for your donation. If you select the option to choose a Regional Charity Pool,
          the selections are pre-determined and cannot be adjusted, removed or subsituted.
          <a href="#" style="text-decoration: underline;">Learn More</a>
        </div>
    </div> --}}
    <p class="p-1"></p>
    @if($errors->any())
        <div class="alert alert-warning">
            @foreach (array_unique($errors->all()) as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{route('donate.save.regional-pool')}}" method="post" class="px-4">
        @csrf

        <div class="row row-cols-1 row-cols-md-3">
            @foreach( $pools as $pool )
            <div class="col mb-4">

                <div class="card h-100 {{ $pool->id == $regional_pool_id ? 'active' : '' }}" data-id="pool{{ $pool->id }}">
                    {{-- <img src="https://picsum.photos/200" class="card-img-top" alt="..."
                             width="50" height="50"> --}}
                    <div class="card-body m-1 p-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="regional_pool_id" id="pool{{ $pool->id }}"
                                value="{{ $pool->id }}" {{ $pool->id == $regional_pool_id ? 'checked' : '' }}>
                            <label class="form-check-label h5 pl-3" for="xxxpool{{ $pool->id }}">
                                {{ $pool->region->name }}
                            </label>
                        </div>

                        <div class=" text-right m-2 pt-2" data-id="{{ $pool->id }}">
                            <i class="more-info fas fa-info-circle fa-2x bottom-right" data-id="{{ $pool->id }}"
                                data-name="{{ $pool->region->name }}"></i>
                        </div>
                    {{-- <div class="pt-2 text-right">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>  --}}
                        {{-- <h5 class="card-title">{{ $pool->region->name  }}</h5>
                        <p class="card-text">&nbsp;</p> --}}
                    </div>
                    {{-- <div class="card-footer mt-0 pl-0 text-right">
                        <i class="fas fa-info-circle fa-2x"></i>
                    </div> --}}
                </div>

            </div>
            @endforeach
        </div>

        <div class="mt-5">
            <a class="btn btn-lg BC-Gov-SecondaryButton" href="{{route('donate.start')}}">Back</a>
            <button class="btn btn-lg btn-primary" type="submit">Next</button>
        </div>

    </form>

</div>

<!-- Modal -->
<div class="modal fade" id="regionalPoolModal" tabindex="-1" role="dialog" aria-labelledby="regionalPoolModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header bg-primary">
            <h5 class="modal-title text-light" id="regionalPoolModalTitle">Regional Charity Pool -
                    <span class="text-light font-weight-bold"></span></h5>
            <button type="button" class="close" style="color:#fff;" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button type="button" style="color:#000;" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>


@endsection

@push('css')

<style>
    .card  {
        color: #1a5a96;
    }

    input[name='regional_pool_id'] {
        width: 18px;
        height: 18px;
    }

    .card:hover {
        background-color: darkgray;
        color: white;
    }
    .card.active {
        background-color: #1a5a96;
        color: white;
    }

    .bottom-right {
        position: absolute;
        bottom: 8px;
        right: 8px;
    }

</style>


@endpush

@push('js')

<script>
$( function() {
    $('.card').click( function(event) {
        event.stopPropagation();

        id = $(this).attr('data-id');

        console.log('radio button clicked -- ' + event.target.id);
            console.log('radio button clicked -- ' + $(this).attr('data-id') );

        if (id) {

            // Need to set the selection on card
            $('.card').each(function( index, element ) {
                // console.log( index + ": " + $( this ).val() + " - " + event.target.id );
                $(element).removeClass('active');
                $(element).prop('checked',false);
            });

            $('.card[data-id=' + id + ']').addClass('active');
            $('#'+id).prop('checked',true);
        }
    });


    $('.more-info').click( function(event) {
        event.stopPropagation();
        // var current_id = event.target.id;
        id = $(this).attr('data-id');
        name = $(this).attr('data-name');

        console.log( 'more info - ' + id );
        if ( id  ) {
            // Lanuch Modal page for listing the Pool detail
            $.ajax({
                url: '/donate/regional-pool-detail/' + id,
                type: 'GET',
                // data: $("#notify-form").serialize(),
                dataType: 'html',
                success: function (result) {
                    $('.modal-title span').html(name);
                    target = '.modal-body';
                    $(target).html('');
                    $(target).html(result);
                },
                complete: function() {
                },
                error: function () {
                    alert("error");
                    $(target).html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
                }
            })

            $('#regionalPoolModal').modal('show')
        }
    });


});
</script>

@endPush

