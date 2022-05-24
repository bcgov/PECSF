@extends('donate.layout.main')

@section ("step-content")
<h3 class="mt-5">1. Select your preferred method for choosing charities</h3>
<div>
    <p class="p-1"></p>
    <div class="card mx-3 pl-3 bg-primary">
        <div class="card-body bg-light">
          If you select the option to choose from the CRA Charity List, you can select up to 10 difference
          charities of your choice for your donation. If you select the option to choose a Regional Charity Pool, 
          the selections are pre-determined and cannot be adjusted, removed or subsituted. 
          <a href="#" style="text-decoration: underline;">Learn More</a>
        </div>
    </div>
    <p class="p-1"></p>
    @if($errors->any())
        <div class="alert alert-warning">
            @foreach (array_unique($errors->all()) as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{route('donate.save.pool-option')}}" method="post" class="px-4">
        @csrf

        <div class="card btn btn-outline-primary text-left {{ $pool_option == "C" ? 'active' : '' }}" id="card-pool1">
            <div class="card-body p-2 ">
                <div class="form-check ">
                    <input class="form-check-input" type="radio" name="pool_option" id="pool1" value="C" 
                        {{ $pool_option == "C" ? 'checked' : '' }}>
                    <label class="form-check-label h5" for="pool1">
                        Select up to 10 charities from the CRA List
                    </label>
                </div>          
            </div>
        </div>

        <div class="card btn btn-outline-primary text-left {{ $pool_option == "P" ? 'active' : '' }}" id="card-pool2">
            <div class="card-body p-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="pool_option" id="pool2" value="P" 
                        {{ $pool_option == "P" ? 'checked' : '' }}>
                    <label class="form-check-label h5" for="pool2">
                        Select a Regional Charity Pool
                    </label>
                </div>          
            </div>
        </div>

        <div class="mt-5">
            <button  name="cancel" value='cancel' class="btn btn-lg btn-outline-primary">Cancel</button>
            <button class="btn btn-lg btn-primary" type="submit">Next</button>
        </div>

    </form>

</div>
@endsection

@push('css')
@endpush

@push('js')

<script>
    $( function() {
        $('.card').click( function(event) {
            // var current_id = event.target.id; 
            var option = this.id;
            
            if (option == 'card-pool1') {
                $('#card-pool1').addClass('active');
                $('#card-pool2').removeClass('active');
                $('#pool1').prop('checked',true);
            } else {
                $('#card-pool1').removeClass('active');
                $('#card-pool2').addClass('active');
                $('#pool2').prop('checked',true);
            }
            // ...do something...
            event.stopPropagation();
        });
    });
</script>

@endPush 

