<form id="bank_deposit_form" action="{{ route("bank_deposit_form") }}" method="POST"
      enctype="multipart/form-data">

    @csrf

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="organization_code">Organization Code:</label>
            <input type="text" class="form-control errors" name="organization_code" id="organization_code" placeholder="">

            <span class="organization_code_errors errors">
                          @error('organization_code')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                    </span>


        </div>
        <div class="form-group col-md-4">
            <label for="form_submitter">Form Submitter:</label>
            <div id="form_submitter">{{$current_user->name}}</div>
            <input type="hidden" value="{{$current_user->id}}" name="form_submitter" />

            <span class="form_submitter_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>
        <div class="form-group col-md-4">
            <label for="campaign_year">Campaign Year:</label>
            <div id="campaign_year">{{$campaign_year->calendar_year}}</div>
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
            <h2 class="blue">Event Details</h2>
    </div>

    <div class="form-row form-body">
        <div class="form-group col-md-6">
            <label for="description">Event Name:</label>
            <input class="form-control" type="text" name="description" id="description" />
            <span>*Include Event Name-Date (DD/MM/YYYY) - Name of Coordinator</span>
            <span class="description_errors errors">
                       @error('description')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
        </div>
        <div id="pecsfid" class="form-group col-md-6">
            <label for="pecsf_id">PECSF ID:</label>
            <input class="form-control" type="text" name="pecsf_id" id="pecsf_id" />
            <span class="pecsf_id_errors errors">
                       @error('pecsf_id')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
        </div>
        <div id="bcgovid" class="form-group col-md-6" style="display:none;">
            <label for="bc_gov_id">BC Gov ID:</label>
            <input class="form-control" type="text" name="bc_gov_id" id="bc_gov_id" />
            <span class="bc_gov_id_errors errors">
                       @error('bc_gov_id')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
        </div>

        <div class="form-group col-md-3">
            <label for="event_type">Event Type:</label>
            <select class="form-control" type="text" id="event_type" name="event_type">
                <option value="Cash One-Time Donation">Cash One-Time Donation</option>
                <option value="Cheque One-Time Donation">Cheque One-Time Donation</option>
                <option value="Fundraiser">Fundraiser</option>
                <option value="Gaming">Gaming</option>
            </select>
            <span class="event_type_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>
        <div class="form-group col-md-3">
            <label for="sub_type">Sub Type:</label>
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
            <label for="sub_type">Deposit Date:</label>
            <input class="form-control" type="date" id="deposit_date" name="deposit_date">
            <span class="deposit_date_errors errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

        <div class="form-group col-md-3">
            <label for="sub_type">Deposit Amount:</label>
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
            <h2 class="blue">Work Location</h2>
    </div>
    <div class="form-row form-body">

        <div class="form-group col-md-4">
            <label for="event_type">*Employment City:</label>
            <select class="form-control search_icon" type="text" id="employment_city" name="employment_city" >
                @foreach($cities as $city)
                    <option value="{{$city->city}}">{{$city->city}}</option>
                    @endforeach
            </select>

            <span class="employment_city_errors errors">
                       @error('employment_city')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>
        <div class="form-group col-md-4">
            <label for="region">*Region:</label>
            <select class="form-control search_icon" id="region" name="region">
                @foreach($regions as $region)
                    <option value="{{$region->id}}">{{$region->name}}</option>
                @endforeach
            </select>
            <span class="region_errors errors">
                       @error('region')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

        <div class="form-group col-md-4">
            <label for="sub_type">Business Unit:</label>
            <select class="form-control search_icon" id="business_unit" name="business_unit">
                @foreach($business_units as $bu)
                    <option value="{{$bu->id}}">{{$bu->name}}</option>
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
    <div class="form-row form-header">
            <h2 class="blue">Mailing address for charitable receipt</h2>
    </div>
    <div class="form-row form-body">

        <div class="form-group col-md-12" id="address_line_1" style="">
            <label for="event_type">Address Line 1:</label>
            <input class="form-control" type="text" id="address_1" name="address_1"/>

            <span class="address_1_errors errors">
                       @error('address_1')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>


        <div class="form-group col-md-4">
            <label for="sub_type">City:</label>
            <input class="form-control" type="text" id="city" name="city">
            <span class="city_errors errors">
                       @error('city')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

        </div>

        <div class="form-group col-md-4">
            <label for="sub_type">Province:</label>
            <select class="form-control" type="text" id="province" name="province">
                <option value="Alberta">Alberta</option>
                <option value="British Columbia">British Columbia</option>
                <option value="Manitoba">Manitoba</option>
                <option value="New Brunswick">New Brunswick</option>
                <option value="Newfoundland and Labrador">Newfoundland and Labrador</option>
                <option value="Nova Scotia">Nova Scotia</option>
                <option value="Nunavut">Nunavut</option>
                <option value="Prince Edward Island">Prince Edward Island</option>
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
            <label for="sub_type">Postal Code:</label>
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
            <h2 class="">Charity selections and distribution</h2>
    </div>

    <div class="form-row form-body">
        <div class="form-group col-md-12">
            <input type="radio" checked id="charity_selection_1" name="charity_selection" value="fsp" />
            <label class="blue" for="charity_selection_1">Fund Supported Pool</label>
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

                <div class="card h-100 {{ $pool->id == $regional_pool_id ? 'active' : '' }}" data-id="pool{{ $pool->id }}">
                    {{-- <img src="https://picsum.photos/200" class="card-img-top" alt="..."
                             width="50" height="50"> --}}
                    <div class="card-body m-1 p-2">

                        <img class="col-md-10" style="height:80px;float-left;" src="img/uploads/{{$pool->image}}"/>

                        <div class="form-check float-right">
                            <input class="form-check-input" type="radio" name="regional_pool_id" id="pool{{ $pool->id }}"
                                   value="{{ $pool->id }}" {{ $pool->id == $regional_pool_id ? 'checked' : '' }}>

                        </div>
                        <br>

                        <label style="font-weight:bold;font-size:12px;" class="form-check-label pl-3" for="xxxpool{{ $pool->id }}">
                            {{ $pool->region->name }}
                        </label>

                    </div>


                </div>

            </div>
        @endforeach
        <div class="form-group col-md-6">
            <input type="radio" id="charity_selection_2" name="charity_selection" value="dc" />
            <label class="blue" for="charity_selection_2">Donor Choice:</label>
        </div>
        <div class="form-group  col-md-6">
            <a href="https://apps.cra-arc.gc.ca/ebci/hacc/srch/pub/dsplyBscSrch?request_locale=en" target="_blank"><img class="float-right" style="width:26px;height:26px;position:relative;top:-4px;" src="{{asset("img/icons/external_link.png")}}"></img><h5 class="blue float-right">View CRA Charity List</h5></a>
        </div>
        <table id="organizations" style="display:none;width:100%">
            @include('volunteering.partials.add-organization', ['index' => 0])
        </table>

        <div class="form-group pointer col-md-12" id="add_row">
            <h5 class="blue"> <i class="fas fa-plus"></i>&nbsp;Add Another Organization</h5>
        </div>
    </div>
<br>



    <div class="form-row form-header">
            <h5 class="blue">Attachment</h5>
            <span class="attachment_errors errors">
                       @error('attachments')
                        <span class="invalid-feedback">{{  $message  }}</span>
                            @enderror
                        </span>
    </div>

    <div class="form-row form-body">
        <div class="form-group col-md-12">

            <table class="table">
                <thead>
                <tr>
                    <th class="blue"></th>
                    <th class="blue">Attached File</th>
                    <th class="blue">Add Attachment</th>
                    <th class="blue">Delete Attachment</th>
                    <th class="blue">View Attachment</th>
                    <th class="blue"></th>

                </tr>
                </thead>
                <tbody>
                <tr class="attachment" id="attachment1">
                    <td>
                                    <span class="attachment_errors  errors">
                       @error('attachment.0')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
                    </td>
                    <td><span class="filename"></span></td>
                    <td><label class="btn btn-primary" for="attachment_input_1"><input style="display:none" id="attachment_input_1" name="attachments[]" type="file" />Add</label></td>
                    <td></td>
                    <td><button class="btn btn-primary">View</button></td>
                    <td><i class="fas fa-plus add_attachment_row"></i></td>
                </tr>


                </tbody>
            </table>

        </div>
    </div>
<br>
    <br>
    <input type="submit" class="btn btn-primary" value="Submit" />
    <br>
    <br>
    <p>Once information has been submitted to PECSF Administration, no further changes are<br> possible through eForm. Please contact pecsf@gov.bc.ca</p>
    <h5>Freedom of Information and Protection of Privacy Act</h5>
    <p>
        Personal information on this form is collected by the BC Public Service Agency for the purposes of processing and reporting your charitable contributions to the Community Fund under section 26(c) of the Freedom of Information and Protection of Privacy Act.
        Questions about the collection of your personal information can be directed to the Campaign Manager, Provincial Employees Community Services Fund at 250 356-1736 or PECSF@gov.bc.ca.
    </p>
</form>
