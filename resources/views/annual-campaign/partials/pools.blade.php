
<div id="step-regional-pools-area" class="p-3">
    <div class="card p-0 pl-2 bg-primary" >
    @if(str_contains(Route::current()->getName(), 'donate-now'))
        <h3>2. Choose your regional fund supported pool></h3>
    @else
        <h3>2. Select your regional charity pool</h3>
    @endif
    <div class="card p-0 pl-2 bg-primary">
        <div class="card-body bg-light">
            By choosing this option your donation will support the designated programs of the regional
            Fund Supported Pool. Click <i class="fas fa-info-circle fa-lg"></i> to learn about the programs in each regional pool.
            {{-- <a href="#" style="text-decoration: underline;">Learn More</a> --}}
        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3">
        @foreach( $fspools as $pool )
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
                </div>
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
            <h5 class="modal-title" id="regionalPoolModalTitle">Regional Charity Pool -
                    <span class="font-weight-bold"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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

@push('css')
<style>

    /* Region Pool Area */
    #step-regional-pools-area .card  {
        color: #1a5a96;
        background-color: #f8fafc;
        border: 1px solid #1a5a96;

    }

    #step-regional-pools-area input[name='regional_pool_id'] {
        width: 18px;
        height: 18px;
    }

    #step-regional-pools-area .card label {
        font-weight: 700;
    }

    #step-regional-pools-area .card:hover {
        /* background-color: darkgray; */
        background-color: #1a5a96;
        opacity: 0.7;
        color: white;
    }
    #step-regional-pools-area .card.active {
        background-color: #1a5a96;
        color: white;
    }

    #step-regional-pools-area .bottom-right {
        position: absolute;
        bottom: 8px;
        right: 8px;
    }



</style>
@endpush

@push('js')
<script>

$(function () {

       // Step 2a -- Regional Pool Area
       $('#step-regional-pools-area .card').click( function(event) {
        event.stopPropagation();

        id = $(this).attr('data-id');

        console.log('radio button clicked -- ' + event.target.id);
        console.log('radio button clicked -- ' + $(this).attr('data-id') );

        if (id) {

            // Need to set the selection on card
            $('#step-regional-pools-area .card').each(function( index, element ) {
                // console.log( index + ": " + $( this ).val() + " - " + event.target.id );
                $(element).removeClass('active');
                $(element).prop('checked',false);
            });

            $('#step-regional-pools-area .card[data-id=' + id + ']').addClass('active');
            $('#'+id).prop('checked',true);
        }
    });


    $('#step-regional-pools-area  .more-info').click( function(event) {
        event.stopPropagation();
        // var current_id = event.target.id;
        id = $(this).attr('data-id');
        name = $(this).attr('data-name');

        console.log( 'more info - ' + id );
        if ( id  ) {
            // Lanuch Modal page for listing the Pool detail
            $.ajax({
                url: '/annual-campaign/regional-pool-detail/' + id,
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
@endpush



