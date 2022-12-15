@extends('adminlte::page')

@section('content_header')

@include('admin-pledge.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Review a Campaign Pledge</h4>
        <div class="flex-fill"></div>
    
        <div class="d-flex">
            <div class="mr-2">
                <x-button class="btn-primary mr-2" :href="route('admin-pledge.campaign.index')"> Back </x-button>        
            </div>
        </div>


    </div>
@endsection

@section('content')

<div class="card pb-4">
  <div class="card-body py-0">

        <div class="pl-3 pt-3">
            <div class="form-group row">
                    <span><b class="pr-3">Transaction ID: </b>{{ $pledge->id }}</span>
            </div>
        </div>
        
        <div class="card m-0 pb-3">
            <div class="card-header bg-light">
                <div class="h5"> Employee Information</div>
            </div>
            <div class="card-body ">
            @if( $pledge->user_id )                        
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="">Organization</label>
                        <input type="text" class="form-control" value="{{ $pledge->organization->name }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="user_emplid">Employee ID</label>
                        <input type="text" class="form-control" value="{{ $pledge->user->primary_job->emplid }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="user_region">Region</label>
                        <input type="text" class="form-control" value="{{ $pledge->user->primary_job->region->name }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="user_dept">Department</label>
                        <input type="text" class="form-control" value="{{ $pledge->user->primary_job->deptid }}" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="user_first_name">First name</label>
                        <input type="text" class="form-control" value="{{ $pledge->user->primary_job->first_name }}" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_last_name">Last name</label>
                        <input type="text" class="form-control" value="{{ $pledge->user->primary_job->last_name }}" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_email">Email</label>
                        <input type="text" class="form-control"  value="{{ $pledge->user->primary_job->email }}" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="user_bu">Business Unit</label>
                        <input type="text" class="form-control" value="{{ $pledge->user->primary_job->bus_unit->name }}" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_org">Organization</label>
                        <input type="text" class="form-control" value="{{ $pledge->user->primary_job->organization }}" disabled>
                    </div>
                </div>
            @else
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="">Organization</label>
                        <input type="text" class="form-control" value="{{ $pledge->organization->name }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">PECSF ID</label>
                        <input type="text" class="form-control" value="{{ $pledge->pecsf_id }}" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="">First name</label>
                        <input type="text" class="form-control" value="{{ $pledge->first_name }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Last name</label>
                        <input type="text" class="form-control" value="{{ $pledge->last_name }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">City</label>
                        <input type="text" class="form-control"  value="{{ $pledge->city }}" disabled>
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

                <div class="pb-1"><b>Calendar year: </b>{{ $pledge->campaign_year->calendar_year }}</div>
                <span><b>Your Bi-weekly payroll deductions:</b></span>
                
                <span class="float-right mb-2">${{ number_format($pledge->goal_amount - $pledge->one_time_amount ,2) }}</span><br>
                <h6>AND / OR</h6>
                <span><b>Your One-time payroll deductions:</b></span>
                
                <span class="float-right">${{ number_format($pledge->one_time_amount,2) }}</span>
                <hr>
                <p class="text-right"><b>Total :</b> ${{ number_format($pledge->goal_amount,2) }}</p>

                <p class="mt-4"><b>Your charitable donations will be disbursed as follows:</b></p>

                @if ( $pledge->type == 'P')
                    <p class="mt-4"><b>Fund Supported Pool: </b>{{  $pledge->fund_supported_pool->region->name  }}</p>
                @endif
                
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                        <th scope="col"></th>
                        <th scope="col" style="width:50%;">Benefitting Charity</th>
                        <th scope="col">Percent</th>
                        <th scope="col">Pay Period Amount</th>
                        <th scope="col">One-Time Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $pay_period_sum = 0; $one_time_sum = 0; @endphp
                        @if ($pledge->type  == 'P')
                            @foreach($pool_charities as $pool_charity)
                                <tr>
                                    <td scope="row">{{ $loop->index +1 }}</td>
                                    <td>
                                        <p>{{ $pool_charity->charity->charity_name }}</p>
                                        <p>{{ $pool_charity->name  }}</p>
                                    </td>
                                    <td class="text-center">{{ number_format($pool_charity->percentage,2) }}%</td>
                                    @if($loop->last)
                                        <td class="th-sm text-center">${{ number_format( ($pledge->goal_amount - $pledge->one_time_amount)  - $pay_period_sum, 2) }}</td>
                                        <td class="text-center">${{ number_format($pledge->one_time_amount - $one_time_sum, 2) }}</td>
                                    @else 
                                        <td class="text-center">${{ number_format( ($pledge->goal_amount - $pledge->one_time_amount) 
                                                                * $pool_charity->percentage / 100, 2) }}</td>
                                        <td class="text-center">${{ number_format($pledge->one_time_amount * $pool_charity->percentage / 100, 2) }}</td>
                                    @endif
                                    @php $pay_period_sum += round( ($pledge->goal_amount - $pledge->one_time_amount) * $pool_charity->percentage / 100, 2);
                                            $one_time_sum += round($pledge->one_time_amount * $pool_charity->percentage / 100, 2); @endphp
                                </tr>
                            @endforeach    
                        @else
                            @foreach($pledges_charities as $pledge_charity)
                                <tr>
                                    <td scope="row">{{ $loop->index +1 }}</td>
                                    <td>
                                        <p>{{ $pledge_charity->charity->charity_name }}</p>
                                        <p>{{ $pledge_charity->additional  }}</p>
                                    </td>
                                    <td class="text-center">{{ number_format($pledge_charity->percentage,2) }}%</td>
                                    <td class="text-right">${{ number_format($pledge_charity->pay_period_amount , 2) }}</td>
                                    <td class="text-right">${{ number_format($pledge_charity->one_time_amount, 2) }}</td>

                                </tr>
                            @endforeach   
                        @endif
                
                    </tbody>
                </table>

            </div>
        </div>

        {{-- Audit Information  --}}
        <div class="card m-0 pb-3">
            <div class="card-header bg-light">
                <div class="h5">Audit Information</div>
            </div>
            <div class="card-body ">
                <div class="row no-gutters">
                    <div class="col-3">
                        <p>Created by: 
                            {{ $pledge->created_by->name }} </p>
                    </div>
                    <div class="col-3">
                        <p>Created at: 
                            {{ date_timezone_set($pledge->created_at, timezone_open('America/Vancouver')) }}
                               </p>
                    </div>
                  </div>
  
                <div class="row">
                  <div class="col-3">
                    <p>Modified by: 
                        {{ isset($pledge->updated_by) ? $pledge->updated_by->name : ''}} </p>
                  </div>
                  <div class="col-3">
                    <p>Modified at: 
                        {{ date_timezone_set($pledge->updated_at, timezone_open('America/Vancouver')) }} </p>
                  </div>
                </div>

            </div>
        </div>
    
  </div>
</div>
@endsection




