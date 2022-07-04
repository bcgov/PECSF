<div class="accordion pt-3" id="method-selection">
    <div class="card m-0">
        <div class="card-header bg-light">
            <div class="custom-control custom-radio">
                <input data-toggle="collapse" data-target="#method-selection-1" type="radio" 
                  name="pool_option" id="pool-option-1" value="P" class="custom-control-input" 
                      {{ $pool_option == "P" ? 'checked' : '' }}/>
                <label class="custom-control-label" for="pool-option-1">Select a Regional Fund Supported Pool</label>
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
</div>