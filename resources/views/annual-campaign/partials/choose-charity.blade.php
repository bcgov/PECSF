<div id="step-charities-area">
    <div class=" form-row">

        <div class="form-group org_hook col-md-4">
                    <label for="keyword">Search by Keyword</label>
                    <input class="form-control" type="search" name="keyword" value="" id="keyword" />
                </div>
                <div class="form-group org_hook col-md-4">
                    <label for="category">Search by Category</label>
                    <select class="form-control" style="width:100%;" type="text" name="category" id="category">
                        <option value="">Choose a Category</option>

        @foreach(\App\Models\Charity::CATEGORY_LIST as $key => $value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
            </select>
            </div>
            <div class="form-group org_hook col-md-4">
                <label for="category">Search by Province</label>
                <select class="form-control" style="width:100%;" type="text" name="province" id="charity_province">
                    <option value="">Choose a Province</option>
                    @foreach(\App\Models\Charity::PROVINCE_LIST as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>

            @isset($fund_support_pool_list)
                <div class="form-group col-md-4">
                    <label for="pool_selection_id">Search by Fund Support Pool</label>
                    <select class="form-control" style="width:100%;" type="text" name="pool_filter" id="pool_filter">
                        <option value="">Choose a Fund Support Pool</option>
                        @foreach($fund_support_pool_list as $pool)
                            <option value="{{ $pool->id }}">{{ $pool->region->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endisset

            <div id="charities_select_area" class="charity-container {{str_contains( Route::current()->getName(), 'bank_deposit_form') ? '' : 'card'}} form-group org_hook  col-md-12">
                {{-- <h4 class="blue" style="padding-left:8px;">Search Results</h4> --}}
                @include("volunteering.partials.organizations")
            </div>
            <div class="col-md-3"></div>
                <br>
                <br>

        <div class="charity-error-hook charity-container {{str_contains( Route::current()->getName(), 'bank_deposit_form') ? '' : 'card'}} form-group org_hook  col-md-12">

                <h4 class="blue" style="padding-left:5px;">Your Charities</h4>
            {{-- <div class="error max-charities-error" style="display:none;"><i class="fas fa-exclamation-circle"></i>
                 Please select a maximum of 10 charities</div> --}}
                <div class="min-charities-error mx-2"></div>


                <div id="selectedcountresults" class="float-right mr-2 text-secondary font-weight-bold"
                    style="{{ count($selected_charities) == 0 ? 'display:none;' : '' }}">{{ count($selected_charities) }} item(s) selected
                </div>
                <h5 style="width:100%;text-align:center; {{ count($selected_charities) > 0 ? 'display:none;' : '' }}"
                    id="noselectedresults" class="align-content-center">You have not chosen any charities</h5>
                <span class="charity_errors errors"></span>

                {{-- @if(count($selected_charities) > 0) --}}
                <table class="charity-container" id="organizations" style="width:100%">
                    @foreach($selected_charities as $index => $charity)
                        @include('annual-campaign.partials.add-charity', ['index' => $index,'charity' => $charity] )

                    @endforeach
                </table>
                {{-- @else
                    <h5 style="width:100%;text-align:center" id="noselectedresults" class="align-content-center">You have not chosen any charities</h5>
                    <span class="charity_errors"></span> --}}
                {{-- @endif --}}

        </div>
        <div class="modal fade" id="charityDetails" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <table class="table">
                        <tr>
                            <td rowspan = 3 style="width:300px;"><img style="width:300px;" src="data:image/jpeg;base64,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" /></td>
                            <td id="modal-charity_name">test title</td>
                            <td rowspan = 3>  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button></td>


                        </tr>
                        <tr>

                            <td id="modal-charity_type">a decription</td>
                        </tr>
                        <tr>

                            <td id="modal-registration_number">98798798</td>
                        </tr>

                    </table>
                </div>

            </div>
        </div>


    </div>
</div>
