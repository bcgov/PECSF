<h3 class="mt-1">Step 1 - Volunteer Details</h3>

<div class="pt-2" id="volunteer-details-area">

    <div class="form-row">
        <div class="form-group col-12 pt-2">
            <label for="business_unit_code">Your Organization</label>
            <select type="text" class="form-control w-75" name="business_unit_code" id="business_unit_code"
                placeholder="" role="listbox">
                <option value="" selected="selected">Choose an Organization</option>
                @foreach($business_units as $bu)
                    @if ($profile) 
                        <option role="listitem" {{ ($profile && $profile->business_unit_code == $bu->code) ? "selected":""}} value="{{$bu->code}}">{{$bu->name}}</option>
                    @else
                        <option role="listitem" {{ ($user->primary_job->business_unit == $bu->code) ? "selected":""}} value="{{$bu->code}}">{{$bu->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        @if ($is_renew) 
            <div class="form-group col-12" style="display:none;">
                <input type="text" class="form-control w-75" name="no_of_years" id="no_of_years" value="1" readonly>
             </div>
        @else
            <div class="form-group col-12">
                <label for="no_of_years">How many years have you been volunteering with PECSF?</label>
                <select type="text" class="form-control w-75" name="no_of_years" id="no_of_years" role="listbox">
                    <option role="listitem" value="" >Please choose the number of years</option>
                    <option role="listitem" value="1" {{ ($profile && $profile->no_of_years == 1) ? "selected" : '' }}>This is my first year!</option>
                    @foreach ( range(2,50) as $value ) 
                        <option role="listitem" value="{{ $value }}" 
                        {{ ($profile && $profile->no_of_years == $value) ? "selected" : "" }}>
                        {{ $value }}</option>
                    @endforeach
                </select>
            </div>
        @endif


        <div class="form-group col-12">
            <label for="preferred_role">Your Preferred Volunteer Role</label>
            <select type="text" class="form-control w-75" name="preferred_role" id="preferred_role"
                 role="listbox">
                <option role="listitem" value="">Please select</option>
                @foreach( $role_list as $key => $value)
                    <option role="listitem" value="{{ $key }}" {{ ($profile && $profile->preferred_role == $key) ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach 
            </select>
        </div>

    </div>
</div>


@push('js')

<script>
    $( function() {
        // $('#preferred-method-area .card').click( function(event) {
        //     // var current_id = event.target.id;
        //     var option = this.id;

        //     if (option == 'card-pool1') {
        //         $('#card-pool1').addClass('active');
        //         $('#card-pool2').removeClass('active');
        //         $('#pool1').prop('checked',true);
        //     } else {
        //         $('#card-pool1').removeClass('active');
        //         $('#card-pool2').addClass('active');
        //         $('#pool2').prop('checked',true);
        //     }
        //     // ...do something...
        //     event.stopPropagation();
        // });


        // // Enter or space key on Wizard STEP icon to forward and backward 
        // $('#preferred-method-area .card').on('keyup', function(e) {
        //     var key  = e.key;
        //     if (key === ' ' || key === 'Enter') {
        //         e.preventDefault();
        //         $(this).trigger('click');
        //     }
        // });

    });
</script>

@endPush
