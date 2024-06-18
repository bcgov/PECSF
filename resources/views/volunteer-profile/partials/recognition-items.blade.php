<h3 class="mt-1">Step 2 - Recognition Items</h3>

<div class="pt-2" id="recognition-items-area">

    {{-- <div class="form-row">
        <div class="form-group col-12 pt-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="address_type" id="address_type_1" value="G" 
                    {{ ($profile && $profile->address_type == 'G') ? 'checked' : '' }}>
                <label class="form-check-label font-weight-bold" for="address_type_1">
                    Use my Global Address Listing
                </label>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-12 ">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="address_type" id="address_type_2" value="S"
                    {{ (($profile && $profile->address_type != 'G') || (!$profile)) ? 'checked' : '' }}>
                <label class="form-check-label font-weight-bold" for="address_type_2">
                    Use the following address:
                </label>
            </div>
        </div>
    </div> --}}

    {{-- 2024 May 29 Suspense Address Type options until the Glowbal address issue resolved --}}
    <input type="hidden" name="address_type" value="S">
    <h5 class="font-weight-bold pt-2">Use the following address:</h5>

    <div class="pl-4 pt-3" id="other_address_area" style="{{ ($profile && $profile->address_type == 'G') ? 'display:none;' : '' }}"> 
        <div class="form-row">
                <div class="form-group col-12">
                    <label for="address">Street address</label>
                    <input id="address" name="address" type="text" value="{{ $profile ? $profile->address : '' }}" class="form-control">
                </div>
        </div>

        <div class="form-row">
            <div class="form-group col-5">
                <label for="city">City</label>
                <select id="city" name="city" class="form-control" role="list" style="width: 100%" aria-hidden="true">
                    <option role="listitem" value="">Select a City</option>
                    @foreach ($cities as $city)
                        <option role="listitem" value="{{ $city->id }}" {{ ($profile && $profile->city == $city->city) ? 'selected' : '' }}  >{{ $city->city }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">

                <label for="province">Province</label>
                <select id="province" class="form-control" name="province" role="list">
                    <option role="listitem" value="">Select a Province</option>
                    @foreach( $province_list as $key => $value)
                        <option role="listitem" value="{{ $key }}" {{ ($profile && $profile->province == $key) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach 
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="postal_code">Postal Code</label>
                <input type="text" class="form-control" name="postal_code" value="{{ $profile ? $profile->postal_code : '' }}">
            </div>
        </div>

    </div>            

    <div class="form-group col-12">
        <hr>
    </div>
    
    <div class="form-group col-12">
        <label>
            <input id="opt_out_recongnition" name="opt_out_recongnition" 
                type="checkbox" value="Y" {{ ($profile && $profile->opt_out_recongnition == "Y") ? "checked":""}}>  
            <span class="pl-2">I wish to opt-out from receiving recognition items.</span>
        </label>
    </div>

</div>

@push('css')

@endpush


@push('js')
<script>

$(function () {
    
    $('#recognition-items-area').on('change', 'input[name=address_type]', function (e) {

        if (this.value == 'G') {
            $('#other_address_area').hide();
            $('input[name=address]').attr('disabled',true);
            $('select[name=city]').attr('disabled',true);
            $('select[name=province]').attr('disabled',true);
            $('input[name=postal_code]').attr('disabled',true);
        } else {
            $('#other_address_area').show();
            $('input[name=address]').prop('disabled',false);
            $('select[name=city]').prop('disabled',false);
            $('select[name=province]').prop('disabled',false);
            $('input[name=postal_code]').prop('disabled',false);
        }

    });

    $('#opt_out_recongnition').on('click', function(e) {
         if (e.originalEvent ) {
            val = this.checked ? this.value : '';
            if (val == 'Y') {
                $('input[name=address]').val('');
                $('select[name=city]').val('').trigger('change');
                $('select[name=province]').val('');
                $('input[name=postal_code]').val('');

                // clear error messages
                fields = ['address_type', 'address', 'city', 'province', 'postal_code', 'opt_out_recongnition'];

                $.each( fields, function( index, field_name ) {
                    $('#volunteer-profile-form [name='+ field_name +']').nextAll('span.text-danger').remove();
                    $('#volunteer-profile-form [name='+ field_name +']').removeClass('is-invalid');
                });

                $('#city').parent().find('.select2-selection--single').removeClass('is-invalid');
                $('#city').parent().find('span.text-danger').remove();

            }
         }
    });

    $('#recognition-items-area').on('change', 'input[name=address],select[name=city],select[name=province],input[name=postal_code]', function (e) {
        if (e.originalEvent ) {
            $('#opt_out_recongnition').prop('checked', false);
        }
    });

});

</script>
@endpush
