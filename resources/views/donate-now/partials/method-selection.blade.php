{{-- <div class="accordion pt-3" id="method-selection">
    <div class="card m-0">
        <div class="card-header bg-light">
            <div class="custom-control custom-radio">
                <input data-toggle="collapse" data-target="#method-selection-1" type="radio"
                  name="pool_option" id="pool-option-1" value="P" class="custom-control-input"
                      {{ $pool_option == "P" ? 'checked' : '' }}/>
                <label class="custom-control-label" for="pool-option-1">Select a regional Fund Supported Pool</label>
            </div>
        </div>

        <div id="method-selection-1" class="collapse {{ $pool_option == "P" ? 'show' : '' }}" data-parent="#method-selection">
            <div class="card-body">

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="pool_id">Fund Supported Pool</label>
                        <select class="form-control" name="pool_id" id="pool_id">
                            <option value="" selected>Choose a pool</option>
                            @foreach ($fspools as $fspool)
                                <option value="{{ $fspool->id }}"
                                    @if (isset($pledge) && $pool_option == "P")
                                        {{  $pledge->fund_supported_pool->id == $fspool->id ? 'selected' : ''  }}
                                    @endif
                                    >{{ $fspool->region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card m-0">
        <div class="card-header  bg-light">
            <div class="custom-control custom-radio">
                <input data-toggle="collapse" data-target="#method-selection-2" type="radio"
                    name="pool_option" id="pool-option-2" value="C" class="custom-control-input"  {{ $pool_option == "C" ? 'checked' : '' }} />
                <label class="custom-control-label" for="pool-option-2">Select up to 10 charities from the CRA list</label>
            </div>
        </div>
        <div id="method-selection-2" class="collapse {{ $pool_option == "C" ? 'show' : '' }}" data-parent="#method-selection">
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <h6 class='font-weight-bold'>Bi-weekly (pay period) Deduction Amount : </h6>
                    </div>
                    <div class="col-md-1">
                        <span class='font-weight-bold' id="pay_period_figure"></span>
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <h6 class='font-weight-bold'>One-time Deduction Amount</h6>
                    </div>
                    <div class="col-md-1">
                        <span class='font-weight-bold' id="one_time_figure"></span>
                    </div>
                </div>
                <table class="table" id="charity-table">
                    <tbody>

                        @php $charities = ($pool_option == "C" and isset($pledge)) ? $pledge->distinct_charities->pluck('id') : [''] @endphp
                        @foreach ( $charities as $index => $oldCharity)
                        <tr id="charity{{ $index }}">
                            @php  $pledge_charity = ($pool_option == "C" && isset($pledge)) ? $pledge->distinct_charities[$index] : new \App\Models\PledgeCharity  @endphp
                            @include('admin-pledge.campaign.partials.charities', ['index' => $index, 'pledge_charity' => $pledge_charity])
                        </tr>
                        @endforeach
                        <tr id="charity{{ isset($pledge) ? $pledge->distinct_charities->count() + 1 : 1 }}"></tr>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12">
                        <button id="add_row" class="btn btn-primary pull-left">+ Add Row</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<h3 class="mt-1">1. Select your preferred method for choosing charities</h3>
<div id="preferred-method-area">
    <p class="p-1"></p>
    <div class="card p-0 pl-2 bg-primary" >
        <div class="card-body bg-light">
            If you select the CRA charity list option, you can support up to 1  charity of your choice through your donation, if they are registered and in good standing with the Canada Revenue Agency (CRA).

            If you select the regional Fund Supported Pool option, charities and distribution amounts are pre-determined and cannot be adjusted, removed, or substituted. 

            Visit the PECSF webpages to learn more about the <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/charity" style="text-decoration: underline;">Fund Supported Pool</a> option.

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


    <div class="card btn btn-outline-primary text-left {{ $pool_option == "C" ? 'active' : '' }}" id="card-pool1">
        <div class="card-body p-2 ">
            <div class="form-check ">
                <input class="form-check-input" type="radio" name="pool_option" id="pool1" value="C"
                    {{ $pool_option == "C" ? 'checked' : '' }}>
                <label style="font-size:16px;line-height:25.6px;" class="form-check-label" for="pool1">
                    <strong>Select one charity from the CRA List</strong><br>
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

    {{-- <div class="mt-5">
        <button  name="cancel" value='cancel' class="btn btn-lg btn-outline-primary">Cancel</button>
        <button class="btn btn-lg btn-primary" type="submit">Next</button>
    </div> --}}

</div>

@push('js')

<script>
    $( function() {
        $('#preferred-method-area .card').click( function(event) {
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
