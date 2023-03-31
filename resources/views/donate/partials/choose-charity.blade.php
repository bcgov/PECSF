<div class="form-group org_hook col-md-4">
            <label for="keyword">Search by Keyword</label>
            <input class="form-control" type="text" name="keyword" value="" id="keyword" />
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
            <option value="">ChoosFe a Province</option>
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


    <div class="charity-container {{str_contains( Route::current()->getName(), 'bank_deposit_form') ? '' : 'card'}} form-group org_hook  col-md-12">
        @include("volunteering.partials.organizations")
    </div>



        <br>
        <br>

<div class="charity-error-hook  {{str_contains( Route::current()->getName(), 'bank_deposit_form') ? '' : 'card'}} form-group org_hook  col-md-12">

        <h4 class="blue" style="padding-left:5px;">Your Charities</h4>
    <div class="error max-charities-error" style="display:none;"><i class="fas fa-exclamation-circle"></i> Please select a maximum of 10 charities</div>

        <table class="" id="organizations" style="display:block;width:100%">
           @if(count($selected_charities) > 0)
            @foreach($selected_charities as $index => $charity)
                @include('volunteering.partials.add-organization', ['index' => $index,'charity' => $charity] )

            @endforeach
            @else
                <h5 style="width:100%;text-align:center" id="noselectedresults" class="align-content-center">You have not chosen any charities</h5>
                <span class="charity_errors"></span>
            @endif
        </table>
</div>
<div class="modal fade" id="charityDetails" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="regionalPoolModalTitle">Charity Details
                </h5>
                <button type="button" class="close" style="color:#000;" data-dismiss="modal" aria-label="Close">
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
