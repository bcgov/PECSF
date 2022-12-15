
<h3 class="">2. Decide on the amount</h3>

<div class=""></div>
<div  style="" >
    <br>
    <div class="" id="one-time-section">
        <div class="predefined-amounts-one-time">
            <div class="btn-group d-flex mt-3 amounts btn-group-toggle" data-toggle="buttons" role="group" aria-label="Select amount">
                @foreach ($amount_options as $key => $text)
                    <label class="mr-2 btn btn-outline-primary rounded btn-lg d-flex align-items-center justify-content-center" for="amount-one-time-{{ $key}}">
                        @php $isCustom = (!in_array($one_time_amount, [6, 12, 20, 50]))  @endphp
                        <input type="radio" name="one_time_amount" class="btn-check" id="amount-one-time-{{ $key }}" autocomplete="off" {{ (($key == '' && $isCustom ) || ($key == $one_time_amount)) ? 'checked' : '' }} value="{{ $key}}" >
                        <div>
                            <div><b>{{ $text }}</b></div>
                            <small class="frequency-text">One-time</small>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="custom-amount-one-time mt-3" style="{{ (!in_array($one_time_amount, [6, 12, 20, 50])) ? '' : 'display:none '}}">
            <label>
                Custom amount
                <input type="text" name="one_time_amount_custom" class="form-control"  value="{{ $one_time_amount_custom }}">
            </label>
        </div>
    </div>
</div>


@push('css')
    <style>
        .predefined-amounts {
            overflow: auto;
        }
        .amounts > div {
            flex-grow: 1;
            flex-basis: 0;
        }
        .amounts label {
            height: 180px;
        }
        .frequency label {
            width: auto;
        }
    </style>
@endpush

@push('js')
 
    <script>
         $(document).on('change', 'input[type=radio][name=one_time_amount]', function() {
            const amount = $(this).val();
            // const frequency = 'one-time';
            // selectAmount(frequency, amount);
            if (!amount) $(".custom-amount-one-time").show();
            else $(".custom-amount-one-time").hide();
        });
   
    </script>
@endpush
