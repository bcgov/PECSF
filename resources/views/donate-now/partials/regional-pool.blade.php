
<h3 class="mt-1">
    @if(str_contains(Route::current()->getName(), 'donate-now'))
        <h3>2. Choose your regional fund supported pool</h3>
    @else
        <h3>2. Select your regional charity pool</h3>
    @endif
        <p class="p-1"></p>
</h3>
<div>
    <p class="p-1"></p>
    <div class="card p-0 pl-2 bg-primary">
        <div class="card-body bg-light">
          By choosing this option your donation will support the designated programs of the regional
          Fund Supported Pool. Click <i class="fas fa-info-circle fa-lg"></i> to learn about the programs in each regional pool.
          {{-- <a href="#" style="text-decoration: underline;">Learn More</a> --}}
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-warning">
            @foreach (array_unique($errors->all()) as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div id="regional-pool-area" class="row row-cols-1 row-cols-md-3">
        @foreach( $pools as $pool )
        <div class="col mb-4">

            <div class="card h-100 {{ $pool->id == $regional_pool_id ? '' : '' }}" data-id="pool{{ $pool->id }}" tabindex="0">
                {{-- <img src="https://picsum.photos/200" class="card-img-top" alt="..."
                            width="50" height="50"> --}}
                <div class="card-body m-1 p-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pool_id" id="regional_pool{{ $pool->id }}"
                            value="{{ $pool->id }}" {{ $pool->id == $regional_pool_id ? 'checked' : '' }} tabindex="-1">
                        <label style="font-weight:700;" class="form-check-label h5 pl-3" for="xxxpool{{ $pool->id }}">
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
            <button type="button" class="btn btn-outline-primary" style="color:#000;" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>


@push('css')

<style>
    #regional-pool-area .card  {
        color: #1a5a96;
        background-color:  #f8fafc;
        border: 1px solid #1a5a96;
    }

    #regional-pool-area input[name='pool_id'] {
        width: 18px;
        height: 18px;
    }

    #regional-pools-area .card label {
        font-weight: 700;
    }

    #regional-pool-area .card:hover {
        /* background-color: darkgray; */
        background-color: #1a5a96;
        opacity: 0.7;
        color: white;
    }
    #regional-pool-area .card.active {
        background-color: #1a5a96;
        color: white;
    }

    #regional-pool-area .bottom-right {
        position: absolute;
        bottom: 8px;
        right: 8px;
    }

</style>
@endpush


@push('js')
<script>
$( function() {

    // Enter or space key on Wizard STEP icon to forward and backward 
    $('#regional-pool-area .card').on('keyup', function(e) {
        // Enter or space key on Wizard STEP icon to forward and backward    
        var key  = e.key;
        if (key === ' ' || key === 'Enter') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    $('#regional-pool-area .card').click( function(event) {
        event.stopPropagation();

        id = $(this).attr('data-id');

        // console.log('radio button clicked -- ' + event.target.id);
        // console.log('radio button clicked -- ' + $(this).attr('data-id') );

        if (id) {

            // Need to set the selection on card
            $('#regional-pool-area .card').each(function( index, element ) {
                // console.log( index + ": " + $( this ).val() + " - " + event.target.id );
                $(element).removeClass('active');
                $(element).prop('checked',false);
            });

            $('#regional-pool-area .card[data-id=' + id + ']').addClass('active');
            $('#regional_'+id).prop('checked',true);
        }
    });


    $('#regional-pool-area .more-info').click( function(event) {
        event.stopPropagation();
        // var current_id = event.target.id;
        id = $(this).attr('data-id');
        name = $(this).attr('data-name');

        // console.log( 'more info - ' + id );
        if ( id  ) {
            // Lanuch Modal page for listing the Pool detail
            $.ajax({
                url: '/donate-now/regional-pool-detail/' + id,
                type: 'GET',
                // data: $("#notify-form").serialize(),
                dataType: 'html',
                success: function (result) {
                    if(result.indexOf('body class="login-page"') != -1){
                        window.location.href = '/login';
                    }

                    $('#regionalPoolModal .modal-title span').html(name);
                    target = '#regionalPoolModal .modal-body';
                    $(target).html('');
                    $(target).html(result);
                    $('#regionalPoolModal').modal('show')
                },
                error: function () {
                }
            })

        }
    });


});
</script>

@endPush

