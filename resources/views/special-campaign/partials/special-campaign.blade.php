
{{-- <h3 class="mt-1">2. Select your regional charity pool</h3> --}}
<div>
    {{-- <p class="p-1"></p> --}}
    {{-- <div class="card mx-1 pl-1 bg-primary">
        <div class="card-body bg-light">
          By choosing this option your donation will support the designated programs of the regional
          Fund Supported Pool. Click <i class="fas fa-info-circle fa-lg"></i> to learn about the programs in each regional pool.

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

    <div id="special-campaign-area">
        @foreach($special_campaigns as $special_campaign) 
        <div class="card {{ $special_campaign->id == $special_campaign_id ? 'active' : '' }}" 
                data-id="{{ $special_campaign->id }}">
            <div class="card-body">
                <div class="row no-gutters pt-2">
                    <div class="col-md-4">
                        <figure class="logo_image text-center">
                            <img src="{{  asset("img/uploads/special_campaign").'/'. $special_campaign->image }}" class="img-fluid rounded">
                        </figure> 
                    </div>
                    <div class="col-md-7 pl-4">
                        <h4 class="card-text font-weight-bold ">{{ $special_campaign->name }}</h4>
                        <h6 class="card-text">{{ $special_campaign->charity->charity_name  }}</h6>
                        <h6 class="card-text">{{ $special_campaign->charity->city . ', ' . $special_campaign->charity->province }}</h6>
                        <p class="card-text font-weight-bold">{{ $special_campaign->description }}</p>
                        <input class="form-check-input" style="display:none" type="radio" 
                            name="special_campaign_id" id="special_campaign_{{ $special_campaign->id }}"
                            value="{{ $special_campaign->id }}" {{ $special_campaign->id == $special_campaign_id ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>  

</div>



@push('css')

<style>
    #special-campaign-area .card {
        /* color: #1a5a96; */
        /* color: #313132 ;  */
        color: #1a5a96;
        /* background-color:  #f8fafc; */
        /* border: 1px solid #1a5a96; */

        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0;
        margin-bottom: 0px;
     }

    #special-campaign-area .card:hover {
        /* background-color: #ddeaee ; */
        /* color: white; */
        /* background-color: #1a5a96; */
        background-color: #f8f9fa;
        opacity: 0.7;
        /* color: white; */
        color: #495057;
        mix-blend-mode: multiply;
    }

    #special-campaign-area .card:hover img {
        /* background-color: #ddeaee ; */
        /* color: white; */
        mix-blend-mode: multiply;
    }

    #special-campaign-area .card.active {
        /* background-color: #a9c8e5; */
        /* background-color: #fcfcfc;
        border: 1px solid red; */
        background-color: #1a5a96;
        color: white;
    }


    #special-campaign-area .card-body {
        padding: 0.25rem;
    }

</style>
@endpush


@push('js')
<script>
$( function() {

    $('#special-campaign-area .card').click( function(event) {
        event.stopPropagation();

        id = $(this).find('input[name="special_campaign_id"]').val();

        // console.log('radio button clicked -- ' + event.target.id);
        // console.log('radio button clicked -- ' + $(this).attr('data-id') );

        if (id) {

            // Need to set the selection on card
            $('#special-campaign-area .card').each(function( index, element ) {
                // console.log( index + ": " + $( this ).val() + " - " + event.target.id );
                $(element).removeClass('active');
                $(element).prop('checked',false);
            });

            $('#special-campaign-area .card[data-id=' + id + ']').addClass('active');
            $('#special_campaign_'+id).prop('checked',true);
        }
    });


});
</script>

@endPush

