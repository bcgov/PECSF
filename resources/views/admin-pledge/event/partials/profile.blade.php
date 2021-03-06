<div class="card m-0">
  <div class="card-header bg-light">
      <p class="h5">Campaign Year</p>
  </diV>
  <div class="card-body">
          <div class="row">
              <div class="form-group ">
                  <label for="campaign_year_id">Calendar Year : &nbsp&nbsp</label>
                    @isset($pledge)
                        <select id="campaign_year_id" class="form-control" name="campaign_year_id" readonly>
                            <option value="{{ $pledge->campaign_year_id }}" selected>{{ $pledge->campaign_year->calendar_year }}</option>
                        </select>
                    @endisset
                    @empty($pledge)
                        <select id="campaign_year_id" class="form-control" name="campaign_year_id">
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
    <div class="card-header bg-light">
        <p class="h5">Employee Information</p>
    </div>
    <div class="card-body ">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="user_id">Organization</label>
                    @isset($pledge)
                        <select class="form-control" style="width:100%;" name="organization_id" id="organization_id" readonly>
                            <option value="{{ $pledge->organization_id }}" selected>{{ $pledge->organization->name }}</option>
                        </select>
                    @endisset
                    @empty($pledge)
                        <select class="form-control" style="width:100%;" name="organization_id" id="organization_id">
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}" {{ $organization->code == 'GOV' ? 'selected' : '' }}>
                                    {{ $organization->name }}</option>
                            @endforeach
                        </select>
                    @endempty
                
            </div>
            <div class="form-group col-md-7">
                <label for="user">Employee</label>
                @if (isset($pledge))
                        <select class="form-control" name="user_id" id="user" readonly>
                            <option value="{{ $pledge->user_id }}" selected>{{ $pledge->user->name }}</option>
                        </select>
                @else 
                
                    <select class="form-control select2" style="width:100%;" name="user_id" id="user_id">
                        {{-- <option value="" selected>-- choose user --</option> --}}
                    </select>
                @endif
            </div>
        </div>


        <div class="form-row">
            <div class="col-md-2 mb-3">
                <label for="user_emplid">Employee ID</label>
                <input type="text" class="form-control border-0" id="user_emplid" 
                    value="{{ isset($pledge) ? $pledge->user->primary_job->emplid : '' }}" 
                    disabled>
            </div>
            <div class="col-md-5 mb-3">
                <label for="user_region">Region</label>
                <input type="text" class="form-control border-0" id="user_region" 
                    value="{{ isset($pledge) ? $pledge->user->primary_job->region->name . ' (' . $pledge->user->primary_job->region->code . ')'  : '' }}" 
                     disabled>
            </div>
            <div class="col-md-5 mb-3">
                <label for="user_dept">Department</label>
                <input type="text" class="form-control border-0" id="user_dept" 
                    value="{{ isset($pledge) ? $pledge->user->primary_job->dept_name . ' (' . $pledge->user->primary_job->deptid . ')' : '' }}" 
                    disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <label for="user_first_name">First name</label>
                <input type="text" class="form-control border-0" id="user_first_name" 
                    value="{{ isset($pledge) ? $pledge->user->primary_job->first_name : '' }}"
                    disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_last_name">Last name</label>
                <input type="text" class="form-control border-0" id="user_last_name" 
                    value="{{ isset($pledge) ? $pledge->user->primary_job->last_name : '' }}"
                    disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_email">Email</label>
                <input type="text" class="form-control border-0" id="user_email" 
                    value="{{ isset($pledge) ? $pledge->user->primary_job->email : '' }}"
                     disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <label for="user_bu">Business Unit</label>
                <input type="text" class="form-control border-0" id="user_bu" 
                    value="{{ isset($pledge) ? $pledge->user->primary_job->bus_unit->name . ' (' . $pledge->user->primary_job->bus_unit->code . ')' : '' }}" 
                     disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_org">Organization</label>
                <input type="text" class="form-control border-0" id="user_org" 
                    value="{{ isset($pledge) ? $pledge->user->primary_job->organization_name : '' }}" 
                    disabled>
            </div>
        </div>

    </div>
</div>
