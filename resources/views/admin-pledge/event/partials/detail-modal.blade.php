
    <input type="hidden" name="form_id" id="form_id" value="{{ $pledge->id }}" />

    <div class="card">
        <div class="form-row form-header">
        {{-- style="left: 5px;
            position: relative;width:100%;border-top-left-radius:5px;border-top-right-radius:5px;background:#1a5a96;color:#fff;padding-left:15px;padding-top:10px;"> --}}
            <h2>Event bank deposit form</h2>
        </div>
    
        <div class="card-body">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="organization_code">Organization</label>

                    <input type="text" disabled class="form-control " name="organization_code" id="organization_code" value="{{ $pledge->organization->name }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="form_submitter">Form submitter</label>
                    <input type="text" disabled class="form-control" value="{{ $pledge->form_submitted_by->name }}" name="form_submitter" />
                </div>

                <div class="form-group col-md-4">
                    <label for="campaign_year">Campaign year</label>
                    <input type="text" disabled class="form-control"
                        value="{{ $pledge->campaign_year->calendar_year - 1 }}" />
                </div>
            </div>

            <div class="pt-1"></div>    
            <div class="form-row form-header">
                <h3 class="blue">Donation or event details</h3>
            </div>

            <div class="form-row form-body">
                <div class="form-group col-md-6">
                    <label for="description">Donation or event name</label>
                    <input class="form-control" readonly type="text" name="description" id="description" value={{ $pledge->description }} />
                </div>

                <div class="form-group col-md-3 event_type">
                    <label for="event_type">Donation or event type</label>
                    <input class="form-control" readonly type="text" id="event_type" name="event_type" value="{{ $pledge->event_type }}">
                </div>

                <div class="form-group col-md-3 sub_type">
                    <label for="sub_type">Sub type</label>
                    <input class="form-control" readonly type="text" id="sub_type" name="sub_type" value="{{ $pledge->subtype }}">
                </div>

                <div class="form-group col-md-2">
                    <label for="sub_type">Deposit date</label>
                    <input class="form-control" readonly type="date" id="deposit_date" name="deposit_date" value="{{ $pledge->deposit_date->format('Y-m-d') }}">
                </div>

                <div class="form-group col-md-2">
                    <label for="sub_type">Deposit amount ($)</label>
                    <input class="form-control" readonly type="text" id="deposit_amount" name="deposit_amount" value="{{ number_format($pledge->deposit_amount,2) }}" />
                </div>

                <div id="bcgovid" class="form-group col-md-2">
                    <label for="bc_gov_id">Employee ID</label>
                    <input class="form-control" readonly type="text" name="bc_gov_id" id="bc_gov_id" value="{{ $pledge->bc_gov_id }}" />
                </div>

                <div id="employeename" class="form-group col-md-3" style="">
                    <label for="employee_name">Employee Name</label>
                    <input class="form-control" readonly type="text" name="employee_name" id="employee_name" value="{{ $pledge->employee_name }}" />
                </div>

                <div id="pecsfid" class="form-group col-md-3" style="">
                    <label for="pecsf_id">PECSF ID</label>
                    <input class="form-control" readonly type="text" name="pecsf_id" id="pecsf_id" value="{{ $pledge->pecsf_id }}"/>
                </div>

            </div>
        
            <div class="pt-3"></div>    
            <div class="form-row form-header">
                <h3 class="blue">Work location</h3>
            </div>
            <div class="form-row form-body">

                <div class="form-group col-md-4">
                    <label for="event_type">Employment city</label>
                    <input class="form-control search_icon"readonly  type="text" id="employment_city" name="employment_city" value="{{  $pledge->employment_city }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="region">Region</label>
                    <input class="form-control search_icon" readonly  id="region" name="region" value="{{ $pledge->region ? $pledge->region->name : '' }}">
                </div>

                <div class="form-group col-md-4">
                    <label for="sub_type">Business unit</label>
                    <input class="form-control search_icon" readonly id="business_unit" name="business_unit" value="{{ $pledge->bu->name }}">

                </div>
            </div>

            <div class="pt-3"></div>  
            <div class="form-row form-header">
                <h3 class="blue">Mailing address for charitable receipt</h3>
            </div>

            <div class="form-row form-body">
                <div class="form-group col-md-12" id="address_line_1" style="">
                    <label for="event_type">Address line 1</label>
                    <input class="form-control" readonly type="text" id="address_1" name="address_1" value="{{ $pledge->address_line_1 }}" />
                </div>


                <div class="form-group col-md-4">
                    <label for="sub_type">City</label>
                    <input class="form-control" readonly type="text" id="city" name="city" value="{{ $pledge->address_city }}">
                </div>

                <div class="form-group col-md-4">
                    <label for="sub_type">Province</label>
                    <input class="form-control" readonly type="text" id="province" name="province" value="{{  $pledge->address_province }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="sub_type">Postal Code</label>
                    <input class="form-control" readonly type="text" id="postal_code" name="postal_code" value="{{  $pledge->address_postal_code }}" />
                </div>
            </div>

            <div class="pt-3"></div>  
            <div class="form-row form-header pt-2">
                <h3 class="blue">Charity selections and distribution</h3>
            </div>

            <div class="form-row form-body">
                <p class="mt-4"><b>Your charitable donations will be disbursed as follows:</b></p>

                @if ( $pledge->regional_pool_id )
                    <p class="mt-4"><b>Fund Supported Pool: </b>{{ $pledge->fund_supported_pool->region->name  }}</p>
                @endif

                <table class="table">
                    <thead class="thead-light">
                        <tr>
                        <th scope="col"></th>
                        <th scope="col" style="width:50%;">Benefitting Charity</th>
                        <th scope="col">Percent</th>
                        <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $one_time_sum = 0; @endphp
                        @if ( $pledge->regional_pool_id  )
                            @foreach($pool_charities as $pool_charity)
                                <tr>
                                    <td scope="row">{{ $loop->index +1 }}</td>
                                    <td>
                                        <p>{{ $pool_charity->charity->charity_name }}</p>
                                        <p>{{ $pool_charity->name  }}</p>
                                    </td>
                                    <td class="text-center">{{ number_format($pool_charity->percentage,2) }}%</td>
                                    @if($loop->last)
                                        <td class="text-center">${{ number_format($pledge->deposit_amount - $one_time_sum, 2) }}</td>
                                    @else
                                        <td class="text-center">${{ number_format($pledge->deposit_amount * $pool_charity->percentage / 100, 2) }}</td>
                                    @endif
                                    @php $one_time_sum += round($pledge->deposit_amount * $pool_charity->percentage / 100, 2); @endphp
                                </tr>
                            @endforeach
                        @else
                            @foreach($pledge->charities as $pledge_charity)
                                <tr>
                                    <td scope="row">{{ $loop->index +1 }}</td>
                                    <td>
                                        <p>{{ $pledge_charity->charity->charity_name }}</p>
                                        <p>{{ $pledge_charity->specific_community_or_initiative  }}</p>
                                    </td>
                                    <td class="text-center">{{ number_format($pledge_charity->donation_percent,2) }}%</td>
                                    <td class="text-right">${{ number_format($pledge->deposit_amount * $pledge_charity->donation_percent / 100, 2) }}</td>

                                </tr>
                            @endforeach
                        @endif

                    </tbody>
                </table>
            </div>


            <div class="pt-3"></div>  
            <div class="form-row form-header p-2">
                <h3 class="blue">File(s)</h3>
            </div>

            <div class="form-row form-body">
                <div class = "row col-md-12">
                    @foreach($pledge->attachments as $attachment)
                        @if ($attachment && $attachment->original_filename)
                            <div class = "col-12 col-lg-6 col-md-8">{{ $loop->index +1 }} -  {{ $attachment->original_filename }}</div>
                            <div class = "col-12 col-lg-6 col-md-4"><a href="{{  "/bank_deposit_form/download/" . $attachment->id }}">Download</a></div>
                        @endif
                    @endforeach
                 </div>

            </div>

            <div class="pt-3"></div>  
            <div class="form-row form-header p-2">
                <h3 class="blue">Audit Information</h3>
            </div>

            <div class="form-row form-body">

                <div class="row col-md-12">
                    <label for="created_by_name" class="col-sm-3 col-form-label">Created By :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="created_by_name" value="{{ $pledge->created_by->name }}" readonly>
                    </div>

                    <label for="formatted_created_at" class="col-sm-3 col-form-label">Created at :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="formatted_created_at"  value="{{ $pledge->created_at }}" readonly>
                    </div>
                </div>

                <div class="row col-md-12">
                    <label for="updated_by_name" class="col-sm-3 col-form-label">Updated By :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="updated_by_name" value="{{ $pledge->updated_by->name }}" readonly>
                    </div>

                    <label for="formatted_updated_at" class="col-sm-3 col-form-label">Updated at :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="formatted_updated_at" value="{{ $pledge->updated_at }}" readonly>
                    </div>
                </div>

                <div class="row col-md-12">
                    <label for="updated_by_name" class="col-sm-3 col-form-label">Approved By :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="approved_by_name" value="{{ $pledge->approved_by->name }}" readonly>
                    </div>

                    <label for="formatted_updated_at" class="col-sm-3 col-form-label">Approved at :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="approved_at" value="{{ $pledge->approved_at }}" readonly>
                    </div>
                </div>
            </div>

    </div>
    


            
