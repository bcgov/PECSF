<p class="h4 pl-2 pb-2">Summary</p>

<ul class="ml-2 pt-1">
    <li>Review the employee pledge summary</li>
    <li>Click submit to save the entry</li>
</ul>

<div class="card m-0 pb-3">
    <div class="card-header bg-light">
        <div class="h5"> Employee Information</div>
    </div>
    <div class="card-body ">
@if( $user )        
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <label for="user_emplid">Organization</label>
                <input type="text" class="form-control" value="{{ $organization->name }}" disabled>
            </div>
            <div class="col-md-3 mb-3">
                <label for="user_emplid">Employee ID</label>
                <input type="text" class="form-control" value="{{ $user->primary_job->emplid }}" disabled>
            </div>
            <div class="col-md-3 mb-3">
                <label for="user_region">Region</label>
                <input type="text" class="form-control" value="{{ $user->primary_job->city_by_office_city->region->name }}" disabled>
            </div>
            <div class="col-md-3 mb-3">
                <label for="user_dept">Department</label>
                <input type="text" class="form-control" value="{{ $user->primary_job->deptid }}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <label for="user_first_name">First name</label>
                <input type="text" class="form-control" value="{{ $user->primary_job->first_name }}" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_last_name">Last name</label>
                <input type="text" class="form-control" value="{{ $user->primary_job->last_name }}" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_email">Email</label>
                <input type="text" class="form-control"  value="{{ $user->primary_job->email }}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <label for="user_bu">Business Unit</label>
                <input type="text" class="form-control" value="{{ $user->primary_job->bus_unit->name }}" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="user_org">Organization (job)</label>
                <input type="text" class="form-control" value="{{ $user->primary_job->organization }}" disabled>
            </div>
            <div class="col-md-3 mb-3">
                <label for="user_office_city">Office City</label>
                <input type="text" class="form-control"  value="{{ $request->user_office_city }}" disabled>
            </div>
        </div>
@else
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <label for="">Organization</label>
                <input type="text" class="form-control" value="{{ $organization->name }}" disabled>
            </div>
            <div class="col-md-3 mb-3">
                <label for="">PECSF ID</label>
                <input type="text" class="form-control" value="{{ $request->pecsf_id }}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <label for="">First name</label>
                <input type="text" class="form-control" value="{{ $request->pecsf_first_name }}" disabled>
            </div>
            <div class="col-md-3 mb-3">
                <label for="">Last name</label>
                <input type="text" class="form-control" value="{{ $request->pecsf_last_name }}" disabled>
            </div>
            <div class="col-md-3 mb-3">
                <label for="">City</label>
                <input type="text" class="form-control"  value="{{ $request->pecsf_city }}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <label for="">Business unit</label>
                <input type="text" class="form-control" value="{{ $request->pecsf_bu }}" disabled>
            </div>
            <div class="col-md-3 mb-3">
                <label for="">Region</label>
                <input type="text" class="form-control" value="{{ $request->pecsf_region }}" disabled>
            </div>
        </div>

@endif
    </div>
</div>

{{-- Deduction  --}}
<div class="card m-0 pb-3">
    <div class="card-header bg-light">
        <div class="h5">Deductions</div>
    </div>
    <div class="card-body ">

        <div class="pb-1"><b>Calendar year: </b>{{ $campaign_year->calendar_year }}</div>
        <span><b>Your Bi-weekly payroll deductions:</b></span>
        
        <span class="float-right mb-2">${{ number_format($pay_period_amount,2) }}</span><br>
        <h6>AND / OR</h6>
        <span><b>Your One-time payroll deductions:</b></span>
        
        <span class="float-right">${{ number_format($one_time_amount,2) }}</span>
        <hr>
        {{-- <p class="text-right"><b>Total :</b> ${{ number_format($pay_period_amount + $one_time_amount,2) }}</p> --}}

        <p class="mt-4"><b>Your charitable donations will be disbursed as follows:</b></p>

        @if ( $pool_option == 'P')
            <p class="mt-4"><b>Fund Supported Pool: </b>{{  $pool->region->name  }}</p>
        @endif
        
        <table class="table">
            <thead class="thead-light">
                <tr>
                <th scope="col"></th>
                <th scope="col">Benefitting Charity</th>
                <th scope="col">Percent</th>
                <th scope="col">Bi-weekly Total Amount</th>
                <th scope="col">One-Time Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $pay_period_sum = 0; $one_time_sum = 0; @endphp
                @if ($pool_option  == 'P')
                    @php $pool_charities = $pool->charities->sortBy('charity.charity_name') 
                    @endphp        
                    @foreach($pool_charities as $pool_charity)
                        <tr>
                            <td scope="row">{{ $loop->index +1 }}</td>
                            <td>
                                <p>{{ $pool_charity->charity->charity_name }}</p>
                                <p>{{ $pool_charity->name  }}</p>
                            </td>
                            <td class="text-center">{{ number_format($pool_charity->percentage,2) }}%</td>
                            @if($loop->last)
                                <td class="text-center">${{ number_format($pay_period_total_amount - $pay_period_sum, 2) }}</td>
                                <td class="text-center">${{ number_format($one_time_amount - $one_time_sum, 2) }}</td>
                            @else 
                                <td class="text-center">${{ number_format($pay_period_total_amount * $pool_charity->percentage / 100, 2) }}</td>
                                <td class="text-center">${{ number_format($one_time_amount * $pool_charity->percentage / 100, 2) }}</td>
                            @endif
                            @php $pay_period_sum += round($pay_period_total_amount * $pool_charity->percentage / 100, 2);
                                    $one_time_sum += round($one_time_amount * $pool_charity->percentage / 100, 2); @endphp
                        </tr>
                    @endforeach    
                @else
                    @foreach($selected_charities as $charity)
                        <tr>
                            <td scope="row">{{ $loop->index +1 }}</td>
                            <td>
                                <p>{{ $charity->charity_name }}</p>
                                <p>{{ $charity->additional  }}</p>
                            </td>
                            <td class="text-center">{{ number_format($charity->percentage,2) }}%</td>
                            @if($loop->last)
                                <td class="text-center">${{ number_format($pay_period_total_amount - $pay_period_sum, 2) }}</td>
                                <td class="text-center">${{ number_format($one_time_amount - $one_time_sum, 2) }}</td>
                            @else 
                                <td class="text-center">${{ number_format($pay_period_total_amount * $charity->percentage / 100, 2) }}</td>
                                <td class="text-center">${{ number_format($one_time_amount * $charity->percentage / 100, 2) }}</td>
                            @endif
                        @php $pay_period_sum += round($pay_period_total_amount * $charity->percentage / 100, 2);
                                $one_time_sum += round($one_time_amount * $charity->percentage / 100, 2); @endphp
                        </tr>
                    @endforeach   
                @endif
        
        
            </tbody>
        </table>

    </div>
</div>


