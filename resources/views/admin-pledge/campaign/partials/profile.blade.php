<div class="card m-0">
  <div class="card-header bg-primary">
      <p class="h5">Campaign Year</p>
  </diV>
  <div class="card-body">
          <div class="row">
              <div class="form-group ">
                  <label for="campaign_year_id">Calendar Year : &nbsp&nbsp</label>
                    @isset($pledge)
                        <select id="campaign_year_id" class="form-control" name="campaign_year_id" style="max-width:200px;" readonly>
                            <option value="{{ $pledge->campaign_year_id }}" selected>{{ $pledge->campaign_year->calendar_year }}</option>
                        </select>
                    @endisset
                    @empty($pledge)
                        <select id="campaign_year_id" class="form-control" name="campaign_year_id" style="max-width:200px;">
                            @foreach ($campaignYears as $cy)
                                <option value="{{ $cy->id }}"
                                    {{ ($cy->calendar_year == date('Y')+1) ? 'selected' : '' }}>
                                    {{ $cy->calendar_year }}
                                </option>
                            @endforeach
                        </select>
                    @endempty
              </div>
          </div>
  </div>
</div>

<div class="card m-0 pb-3">
    <div class="card-header bg-primary">
        <p class="h5">Employee Information</p>
    </div>
    <div class="card-body ">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="user_id">Organization</label>
                    @isset($pledge)
                        @if ( $edit_pecsf_allow )
                            <select class="form-control" style="width:100%;" name="organization_id" id="organization_id">
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}" code="{{ $organization->code }}" {{ $organization->code == $pledge->organization->code ? 'selected' : '' }}>
                                        {{ $organization->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control" style="width:100%;" name="organization_id" id="organization_id" readonly>
                                <option value="{{ $pledge->organization_id }}"  code="{{ $organization->code }}" selected>{{ $pledge->organization->name }}</option>
                            </select>
                        @endif
                    @endisset
                    @empty($pledge)
                        <select class="form-control" style="width:100%;" name="organization_id" id="organization_id">
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}" code="{{ $organization->code }}" {{ $organization->code == 'GOV' ? 'selected' : '' }}>
                                    {{ $organization->name }}</option>
                            @endforeach
                        </select>
                    @endempty

            </div>
            <div class="form-group col-md-7 emplid_section">
                <label for="user">Employee</label>
                @if (isset($pledge))
                        <select class="form-control" name="user_id" id="user" readonly>
                            <option value="{{ $pledge->user_id }}" selected>{{ $pledge->user ? $pledge->user->name : '' }}</option>
                        </select>
                @else
                    <select class="form-control select2" style="width:100%;" name="user_id" id="user_id">
                        {{-- <option value="" selected>-- choose user --</option> --}}
                    </select>
                @endif
            </div>
            <div class="form-group col-md-3 pecsf_id_section">
                <label for="user">PECSF ID</label>
                {{-- @if (isset($pledge))
                    <input type="text" class="form-control" name="pecsf_id" id="pecsf_id" value="{{ $pledge->pecsf_id }}" readonly>
                @else  --}}
                    <input type="text" class="form-control" name="pecsf_id" id="pecsf_id" value="{{ isset($pledge) ? $pledge->pecsf_id : ''}}" {{ $edit_pecsf_allow ? '' : 'readonly' }}>
                {{-- @endif --}}
            </div>

        </div>

        <div class="form-row pecsf_id_section">
            <div class="col-md-3 mb-3">
                <label for="pecsf_first_name">First Name</label>
                <input type="text" class="form-control" id="pecsf_first_name" name="pecsf_first_name"
                    value="{{ old('pecsf_first_name') ?? ( isset($pledge) ? $pledge->first_name : '') }}" {{ $edit_pecsf_allow ? '' : 'readonly' }}>
            </div>
            <div class="col-md-3 mb-3">
                <label for="pecsf_last_name">Last Name</label>
                <input type="text" class="form-control" id="pecsf_last_name" name="pecsf_last_name"
                    value="{{ old('pecsf_last_name') ?? ( isset($pledge) ? $pledge->last_name : '') }}" {{ $edit_pecsf_allow ? '' : 'readonly' }}>
            </div>
            <div class="col-md-3 mb-3">
                <label for="pecsf_city">City</label>
                @if ( $edit_pecsf_allow )
                    <select class="form-control" style="width:100%;" name="pecsf_city" id="pecsf_city" >
                        <option value="">Select a City</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->city }}" {{ $city->city == old('pecsf_city') || (isset($pledge) && $city->city == $pledge->city) ? 'selected' : '' }}
                                        data-region="{{ $city->region ? $city->region->name : '' }}">
                                {{ $city->city }}</option>
                        @endforeach
                    </select>
                @else
                   <input type="text" class="form-control" id="pecsf_city" name="pecsf_city"
                      value="{{ ( isset($pledge) ? $pledge->city : '') }}" readonly>
                @endif
            </div>
            <div class="col-md-3 mb-3">
            </div>
            <div class="col-md-4 mb-4">
                <label for="pecsf_bu">Business Unit</label>
                <input type="text" class="form-control border-0" id="pecsf_bu" name="pecsf_bu"
                    value="{{ (isset($pledge) && $pledge->pecsf_user_bu ) ? $pledge->pecsf_user_bu->name . ' (' . $pledge->pecsf_user_bu->code . ')' : '' }}"
                    readonly>
            </div>
            <div class="col-md-4 mb-4">
                <label for="pecsf_region">Region</label>
                <input type="text" class="form-control border-0" id="pecsf_region" name="pecsf_region"
                    value="{{ (isset($pledge) && $pledge->pecsf_user_region ) ? $pledge->pecsf_user_region->name . ' (' . $pledge->pecsf_user_region->code . ')' : '' }}"
                    readonly>
            </div>
        </div>

        <div class="form-row emplid_section">
            <div class="col-md-2 mb-3">
                <label for="user_emplid">Employee ID</label>
                <input type="text" class="form-control border-0" id="user_emplid"
                        value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->emplid : '' }}"
                    disabled>
            </div>
            <div class="col-md-5 mb-3">
                <label for="user_region">Region</label>
                <input type="text" class="form-control border-0" id="user_region"
                        value="{{ (isset($pledge)) ? ($pledge->pecsf_user_region->name . ' (' . $pledge->pecsf_user_region->code . ')' ) : '' }}"
                     disabled>
            </div>
            <div class="col-md-5 mb-3">
                <label for="user_dept">Department</label>
                <input type="text" class="form-control border-0" id="user_dept"
                        value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->dept_name . ' (' . $pledge->user->primary_job->deptid . ')' : '' }}"
                    disabled>
            </div>
        </div>
        <div class="form-row emplid_section">
            <div class="col-md-4 mb-3">
                <label for="user_first_name">First name</label>
                <input type="text" class="form-control border-0" id="user_first_name"
                    value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->first_name : '' }}"
                    disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_last_name">Last name</label>
                <input type="text" class="form-control border-0" id="user_last_name"
                    value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->last_name : '' }}"
                    disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_email">Email</label>
                <input type="text" class="form-control border-0" id="user_email"
                    value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->email : '' }}"
                     disabled>
            </div>
        </div>
        <div class="form-row emplid_section">
            <div class="col-md-4 mb-3">
                <label for="user_bu">Business Unit</label>
                <input type="text" class="form-control border-0" id="user_bu"
                    value="{{ (isset($pledge)) ? ($pledge->pecsf_user_bu->name . ' (' . $pledge->pecsf_user_bu->code . ')' )  : '' }}"
                     disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_org">Organization</label>
                <input type="text" class="form-control border-0" id="user_org"
                    value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->organization_name : '' }}"
                    disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_office_city">Office City</label>
                <input type="text" class="form-control border-0" id="user_office_city" name="user_office_city"
                    value="{{ (isset($pledge)) ? $pledge->city   : '' }}"
                    readonly>
            </div>
        </div>

    </div>
</div>
