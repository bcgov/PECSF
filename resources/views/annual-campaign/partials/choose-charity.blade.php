<div id="step-charities-area">
    <h3 class="step-charities-error-header">2. Choose your charities (up to 10)</h3>

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
                <div class="form-group col-md-4 org_hook">
                    <label for="pool_selection_id">Search by Fund Supported Pool</label>
                    <select class="form-control" style="width:100%;" type="text" name="pool_filter" id="pool_filter">
                        <option value="">Choose a Fund Supported Pool</option>
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


        <div class="charity-error-hook charity-container {{str_contains( Route::current()->getName(), 'bank_deposit_form') ? '' : 'card'}} form-group org_hook  col-md-12">

                <h4 class="blue" style="padding-left: 25px;
    padding-top: 20px;">Your Charities</h4>
            {{-- <div class="error max-charities-error" style="display:none;"><i class="fas fa-exclamation-circle"></i>
                 Please select a maximum of 10 charities</div> --}}
                <div class="min-charities-error mx-2"></div>


                <div id="selectedcountresults" class="float-right mr-2 text-secondary font-weight-bold"
                    style="{{ count($selected_charities) == 0 ? 'display:none;' : '' }}">{{ count($selected_charities) }} item(s) selected
                </div>
                <h3 style="width:100%;text-align:center; {{ count($selected_charities) > 0 ? 'display:none;' : '' }}"
                    id="noselectedresults" class="align-content-center">You have not chosen any charities</h3>
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
        <div class="modal fade" id="charityDetails" tabindex="-1" role="dialog" aria-labelledby="charityDetailsModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="charityDetailsModalTitle">Charity Details
                                <span class="text-dark font-weight-bold"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <table class="table charity">
                            <tr>
                                <td>Business/Registration Number</td>
                                <td id="registration_number"></td>
                            </tr>
                            <tr>
                                <td>Charity Status</td>
                                <td id="charity_status"></td>
                            </tr>
                            <tr>
                                <td>Effective date of status</td>
                                <td id="effective_date_of_status"></td>
                            </tr>
                            <tr>
                                <td>Sanction</td>
                                <td id="sanction"></td>
                            </tr>
                            <tr>
                                <td>Designation</td>
                                <td id="designation"></td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td id="modalcategory"></td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td id="address"></td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td id="city"></td>
                            </tr>
                            <tr>
                                <td>Province, Territory</td>
                                <td id="province"></td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td id="country"></td>
                            </tr>
                            <tr>
                                <td>Postal code/zip code:</td>
                                <td id="postal_code"></td>
                            </tr>
                            <tr>
                                <td>Website:</td>
                                <td id="uri"></td>
                            </tr>
                            <tr>
                                <td>Charitable Programs</td>
                                <td id="charitable_programs"></td>
                            </tr>
                        </table>
                        <table style="border:none;" class="table fsp">
                            <tr style="border:none;">
                                <td style="border:none;" rowspan="3"><img id="pool_image" /></td>
                                <td style="border:none;" id="pool_name"></td>
                            </tr>
                            <tr style="border:none;">

                                <td style="border:none;" id="pool_description"></td>
                            </tr>
                            <tr style="border:none;">

                                <td style="border:none;" id="pool_registration_number"></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                    </div>    
                </div>

            </div>
        </div>


    </div>
</div>
