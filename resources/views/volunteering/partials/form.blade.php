<form id="bank_deposit_form" action="{{ route("bank_deposit_form") }}" method="POST"
      enctype="multipart/form-data">
    @csrf
    <br>

    <input type="hidden" name="form_id" id="form_id" value="0" />

    <div class="form-row" style="left: 5px;
    position: relative;width:100%;border-top-left-radius:5px;border-top-right-radius:5px;background:#1a5a96;color:#fff;padding-left:15px;padding-top:10px;">
        <h2>Event bank deposit form</h2>
    </div>
    <div class="card" style="border-radius:0px;">
        <div class="card-body">
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="organization_code">Organization code</label>
            <select type="text" class="form-control " name="organization_code" id="organization_code" placeholder="">
            <option value="" selected="selected">Choose and Org Code</option>

            </select>
            <span class="organization_code_errors errors">
                          @error('organization_code')
                            <span class="invalid-feedback">{{  $message  }}</span>
                          @enderror
            </span>
        </div>
        <div class="form-group col-md-4">
            <label for="form_submitter">Form submitter</label>
            <div id="form_submitter">{{$current_user->name}}</div>
            <input type="hidden" value="{{$current_user->id}}" name="form_submitter" />

            <span class="form_submitter_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>
        <div class="form-group col-md-4">
            <label for="campaign_year">Campaign year</label>
            <div id="campaign_year">{{$campaign_year->calendar_year - 1}}</div>
            <input type="hidden" value="{{$campaign_year->id}}" name="campaign_year" />
            <span class="campaign_year_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>
    </div>
<br>
    <div class="form-row form-header">
            <h3 class="blue">Event details</h3>
    </div>

    <div class="form-row form-body">
        <div class="form-group col-md-6">
            <label for="description">Event name</label>
            <input class="form-control" type="text" name="description" id="description" />
            <span>Include Event Name-Date (DD/MM/YYYY) - Name of Coordinator</span>
            <span class="description_errors errors">
                       @error('description')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
        </div>
        <div id="pecsfid" class="form-group col-md-6" style="">
            <label for="pecsf_id">PECSF ID</label>
            <input class="form-control" type="text" name="pecsf_id" id="pecsf_id" />
            <span class="pecsf_id_errors errors">
                       @error('pecsf_id')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
        </div>
        <div id="bcgovid" class="form-group col-md-6" style="display:none;">
            <label for="bc_gov_id">Employee ID</label>
            <input class="form-control" type="text" name="bc_gov_id" id="bc_gov_id" />
            <span class="bc_gov_id_errors errors">
                       @error('bc_gov_id')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
        </div>

        <div class="form-group col-md-3">
            <label for="event_type">Event type</label>
            <select class="form-control" type="text" id="event_type" name="event_type">
                <option value="">Select an event type</option>
                <option value="Cash One-Time Donation">Cash one-time donation</option>
                <option value="Cheque One-Time Donation">Cheque one-time donation</option>
                <option value="Fundraiser">Fundraiser</option>
                <option value="Gaming">Gaming</option>
            </select>
            <span class="event_type_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>
        <div class="form-group col-md-3 sub_type">
            <label for="sub_type">Sub type</label>
            <select class="form-control" type="text" id="sub_type" name="sub_type" disabled="true">
                <option value="false">Disabled</option>
            </select>
            <span class="sub_type_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

        <div class="form-group col-md-3">
            <label for="sub_type">Deposit date</label>
            <input class="form-control" type="date" id="deposit_date" name="deposit_date">
            <span class="deposit_date_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

        <div class="form-group col-md-3">
            <label for="sub_type">Deposit amount ($)</label>
            <input class="form-control" type="text" id="deposit_amount" name="deposit_amount" />

            <span class="deposit_amount_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

    </div>
<br>



    <div class="form-row form-header">
            <h3 class="blue">Work location</h3>
    </div>
    <div class="form-row form-body">

        <div class="form-group col-md-4">
            <label for="event_type">Employment city</label>
            <select onchange="$('#region').val($('[code='+this.options[this.selectedIndex].attributes[0].value+']').attr('value')).trigger('change');" class="form-control search_icon" type="text" id="employment_city" name="employment_city" >
                <option value="">Select a city</option>
                @foreach($cities as $city)
                    <option region="{{$city->TGB_REG_DISTRICT}}" value="{{$city->city}}">{{$city->city}}</option>
                    @endforeach
            </select>

            <span class="employment_city_errors errors">
                       @error('employment_city')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>
        <div class="form-group col-md-4">
            <label for="region">Region</label>
            <select class="form-control search_icon" id="region" name="region">
                <option value="">Select a region</option>
            @foreach($regions as $region)
                    <option  code="{{$region->code}}" value="{{$region->id}}">{{$region->name}}</option>
                @endforeach
            </select>
            <span class="region_errors errors">
                       @error('region')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

        <div class="form-group col-md-4">
            <label for="sub_type">Business unit</label>
            <select class="form-control search_icon" id="business_unit" name="business_unit">
                <option value="">Select a business unit</option>
            @foreach($business_units as $bu)
                    @if(!empty($bu->name))
                    <option value="{{$bu->id}}">{{$bu->name}}</option>
                    @endif
                @endforeach
            </select>
            <span class="business_unit_errors errors">
                       @error('business_unit')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>


    </div>
    <br>
    <div class="form-row form-header address_hook" style="display:none;">
            <h3 class="blue">Mailing address for charitable receipt</h3>
    </div>
    <div class="form-row form-body address_hook" style="display:none;">

        <div class="form-group col-md-12" id="address_line_1" style="">
            <label for="event_type">Address line 1</label>
            <input class="form-control" type="text" id="address_1" name="address_1"/>

            <span class="address_1_errors errors">
                       @error('address_1')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>


        <div class="form-group col-md-4">
            <label for="sub_type">City</label>

            <select class="form-control search_icon" type="text" id="city" name="city" >
                <option value="">Select a city</option>
            @foreach($cities as $city)
                <option value="{{$city->city}}">{{$city->city}}</option>
            @endforeach
            </select>
            <span class="city_errors errors">
                       @error('city')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

        <div class="form-group col-md-4">
            <label for="sub_type">Province</label>
            <select class="form-control" type="text" id="province" name="province">
                <option value="">Select a  province</option>

                <option value="Alberta">Alberta</option>
                <option value="British Columbia">British columbia</option>
                <option value="Manitoba">Manitoba</option>
                <option value="New Brunswick">New brunswick</option>
                <option value="Newfoundland and Labrador">Newfoundland and labrador</option>
                <option value="Nova Scotia">Nova scotia</option>
                <option value="Nunavut">Nunavut</option>
                <option value="Prince Edward Island">Prince edward island</option>
                <option value="Quebec">Quebec</option>
                <option value="Saskatchewan">Saskatchewan</option>
                <option value="Yukon">Yukon</option>

                <option value="Ontario">Ontario</option>
            </select>
            <span class="province_errors errors">
                       @error('province')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>
        <div class="form-group col-md-4">
            <label for="sub_type">Postal Code</label>
            <input class="form-control" type="text" id="postal_code" name="postal_code" />
            <span class="postal_code_errors errors">
                       @error('postal_code')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

    </div>
<br>
    <br>
    <div class="form-row form-header">
            <h3 class="blue">Charity selections and distribution</h3>
    </div>

    <div class="form-row p-3" style="border-left:#ccc 1px solid;border-right:#ccc 1px solid;">
        <div class="form-group col-md-12">
            <input type="radio" checked id="charity_selection_1" name="charity_selection" value="fsp" />
            <label class="blue" for="charity_selection_1">Fund supported pool</label>
            <span class="charity_selection_errors errors">
                       @error('charity_selection')
                        <span class="invalid-feedback">{{  $message  }}</span>
                            @enderror
                        </span>

            <br>
            <span style="padding:20px;">
    By choosing this option your donation will support the current Fund Supported Pool of regional programs. Click on the tiles to learn about the programs in each regional pool.
</span>
        </div>


        @foreach( $pools as $pool )
            <div class="form-group col-md-2 form-pool">

                <div style="width:100%;" class="BC-Gov-SecondaryButton card h-100 {{ $pool->id == $regional_pool_id ? 'active' : '' }}" data-id="pool{{ $pool->id }}">
                    {{-- <img src="https://picsum.photos/200" class="card-img-top" alt="..."
                             width="50" height="50"> --}}
                    <div class="card-body m-1 p-2">

                        <div class="form-check float-left">
                            <input class="form-check-input" type="radio" name="regional_pool_id" id="pool{{ $pool->id }}"
                                   value="{{ $pool->id }}" {{ $pool->id == $regional_pool_id ? 'checked' : '' }}>

                        </div>
                        <br>

                        <label style="font-weight:bold;font-size:16px;text-align: center;
    width: 100%;" class="form-check-label pl-3" for="xxxpool{{ $pool->id }}">
                            {{ $pool->region->name }}
                        </label>
                        <span style="font-size:16px;font-weight:bold;text-decoration:underline;width:100%;text-align:center;display:block" class="more-info bottom-center" data-id="{{ $pool->id }}"
                              data-name="{{ $pool->region->name }}" data-source="" data-type="" data-yearcd="{{date("Y",strtotime($pool->start_date))}}">View Details</span>
                    </div>


                </div>

            </div>
        @endforeach

        @for($i=0;$i<((count($pools)%6) );$i++)
            <div class="form-group col-md-2 form-pool">



            </div>
            @endfor
    </div>
            <div class="form-row p-3"style="border-left:#ccc 1px solid;border-right:#ccc 1px solid;border-bottom:#ccc 1px solid;border-radius:5px;">

        <div class="form-group col-md-6">
            <input type="radio" id="charity_selection_2" name="charity_selection" value="dc" />
            <label class="blue" for="charity_selection_2">Donor choice</label>
        </div>
        <div class="form-group  org_hook col-md-6">
            <a href="https://apps.cra-arc.gc.ca/ebci/hacc/srch/pub/dsplyBscSrch?request_locale=en" target="_blank"><img class="float-right" style="width:26px;height:26px;position:relative;top:-4px;" src="{{asset("img/icons/external_link.png")}}"></img><h5 class="blue float-right">View CRA Charity List</h5></a>
        </div>
        <div class="form-group col-md-12">
            <p>By choosing this option you can support up to 10 Canada Revenue Agency (CRA) registered charitable organizations.
                Our system uses the official name of the charity registered with the CRA. You can use the View CRA Charity List link to confirm if the organization you would like to support is registered. You can also support a specific branch or program name.</p>

        </div>
        @include('donate.partials.choose-charity')




        </div>
            <br>



            <div class="form-row form-header">
                <h3 class="blue">File(s)</h3>

            </div>
            <div class="form-row form-header">
                <span class="attachment_errors errors">
                       @error('attachments')
                        <span class="invalid-feedback">{{  $message  }}</span>
                            @enderror
                        </span>
            </div>
            <div class="form-row form-body">
                <div style="padding:8px;" class="upload-area form-group col-md-3">
                    <i style="color:#1a5a96;" class="fas fa-file-upload fa-5x"></i>
                    <br>
                    <br>
                    <a onclick="$('#attachment_input_1').click();" style="background:#fff;border:none;font-weight:bold;color:#000;text-align:center;" id="upload-area-text" for="attachment_input_1">Drag and Drop Or <u>Browse</u> Files</a>
                    <input style="display:none" id="attachment_input_1" name="attachments[]" type="file" />
                </div>
                <table id="attachments" class=" form-group col-md-6">

                </table>
            </div>

    </div>







<br>
    <br>
    <input type="submit" style="margin-left:20px;"   class="col-md-2 btn btn-primary" value="Submit" />
    <br>
    <br>
    <p style="padding:20px;">Once information has been submitted to PECSF Administration, no further changes are possible through eForm. Please contact pecsf@gov.bc.ca</p>

        <h5 style="padding-left:20px;">Freedom of Information and Protection of Privacy Act</h5>
    <p style="padding:20px;">

        Personal information on this form is collected by the BC Public Service Agency for the purposes of processing and reporting your charitable contributions to the Community Fund and for program evaluation and improvement under sections 26 (c) and (e) of the Freedom of Information and Protection of Privacy Act.

        Questions about the collection of your personal information can be directed to the Campaign Manager, Provincial Employees Community Services Fund at 250 356-1736 or <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>.  </p>
        </div>

</form>
<!-- Modal -->
<div class="modal fade" id="regionalPoolModal" tabindex="-1" role="dialog" aria-labelledby="pledgeDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="pledgeDetailModalTitle">Regional Charity Pool
                    <span class="text-dark font-weight-bold"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pledgeDetail">
            </div>
            <div class="modal-footer">
                <button type="button" style="color:#000;" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

