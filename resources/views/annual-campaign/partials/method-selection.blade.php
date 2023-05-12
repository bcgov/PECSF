<div id="method-selection-area" class="mx-3">
    <p class="p-1"></p>

    <div class="card btn btn-outline-primary text-left {{ $pool_option == "C" ? 'active' : '' }}" id="card-pool1">
        <div class="card-body p-2 ">
            <div class="form-check ">
                <input class="form-check-input" type="radio" name="pool_option" id="pool1" value="C"
                    {{ $pool_option == "C" ? 'checked' : '' }}>
                <label style="font-size:16px;line-height:25.6px;" class="form-check-label" for="pool1">
                    <strong>Select up to 10 charities from the CRA List</strong><br>
                    <span>Explore charities by keyword, category, province, or view charities, and their associated programs, which are part of the Fund Supported Pools.
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="card btn btn-outline-primary text-left {{ $pool_option == "P" ? 'active' : '' }}" id="card-pool2">
        <div class="card-body p-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="pool_option" id="pool2" value="P"
                    {{ $pool_option == "P" ? 'checked' : '' }}>
                <label style="font-size:16px;line-height:25.6px;" class="form-check-label h5" for="pool2">
                    <strong>Select a regional Fund Supported Pool</strong><br>
                    <span>Charities and distribution amounts are pre-determined.</span>
                </label>

            </div>
        </div>
    </div>

</div>


@push('js')
<script>

$(function () {

    // Page  1 -- Pool section
    function update_wizard_step_based_on_pool_option() {
        pool_option = $("input[name='pool_option']:checked").val();
        if ( pool_option == 'P') {
            $('ul.bs4-step-tracking li.amount-distribution').hide();
        } else {
            $('ul.bs4-step-tracking li.amount-distribution').show();
        }
    }

    $('#method-selection-area .card').click( function(event) {
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
        update_wizard_step_based_on_pool_option();

        event.stopPropagation();
    });

    // Initial the wizard page when 1st loaded
    update_wizard_step_based_on_pool_option();

});

</script>
@endpush

