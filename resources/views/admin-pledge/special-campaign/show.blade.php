@extends('adminlte::page')

@section('content_header')

@include('admin-pledge.partials.tabs')

    <h4 class="mx-1 mt-3">Review a Special Campaign Pledge</h4>

    <div class="mx-1 pt-2">
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('admin-pledge.special-campaign.index') }}'">
            Back    
        </button> 
    </div>
    
@endsection

@section('content')

<div class="card pb-4">
  <div class="card-body py-0">

        <div class="d-flex  align-items-center my-2">
            <h4><b>Transaction ID: </b><span>{{ $pledge->id }}</span></h4> 
            @if ($pledge->cancelled)
                <h4 class="ml-3 border border-danger rounded p-2 text-danger font-weight-bold">Cancelled</h4>        
            @endif
            <div class="flex-fill"></div>

            <div class="d-flex  align-items-center ">
                <div class="mr-2">
                </div>
            </div>
        </div>

        <div class="card m-0 mt-3">
            <div class="card-header bg-primary">
                <p class="h5">Calendar Year</p>
            </diV>
            <div class="card-body">
                <div class="row">
                    <div class="form-group ">
                        <label for="yearcd">Calendar Year</label>
                            <select id="yearcd" class="form-control" name="yearcd" style="max-width:200px;" readonly>
                            <option value="{{ $pledge->calendar_year }}" selected>{{ $pledge->yearcd }}</option>
                            </select>
                    </div>
                </div>
            </div>
          </div>
        
        <div class="card m-0 pb-3">
            <div class="card-header bg-primary">
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
        <div class="card m-0 mt-3 pb-0">
            <div class="card-header bg-primary">
                <div class="h5">Deductions</div>
            </div>
            <div class="card-body ">
                       
                <div class="">
                    <div class="row">
                        <div class="col-4 font-weight-bold">Your One-time payroll deductions :</div>
                        <div class="col-8">${{ number_format($pledge->one_time_amount,2) }}</div>
                    </div>
                </div>

                <div class=" mt-2">
                    <div class="row">
                        <div class="col-4 font-weight-bold">Special Campaign :</div>
                        <div class="col-8">{{ $pledge->special_campaign->name }}</div>
                    </div>
                </div>
        
                <div class=" mt-2">
                    <div class="row">
                        <div class="col-4 font-weight-bold">In support of :</div>
                        <div class="col-8">{{ $pledge->in_support_of }}</div>
                    </div>
                </div>
        
                <div class=" mt-2">
                    <div class="row">
                        <div class="col-4 font-weight-bold">Deduction date :</div>
                        <div class="col-8">{{ $pledge->deduct_pay_from }}</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- PeopleSoft Integration Information  --}}
        <div class="card m-0 mt-3 pb-3">
            <div class="card-header bg-primary">
                <div class="h5">PeopleSoft Integration</div>
            </div>
            <div class="card-body ">
                <div class="row no-gutters">
                    <div class="col-3">
                        <p><b>Send Status:</b> 
                            {{ $pledge->ods_export_status == null ? 'Not Started' : 'Completed' }} </p>
                    </div>
                    <div class="col-3">
                        <p><b>Send at:</b> 
                            {{ $pledge->ods_export_at ? $pledge->ods_export_at : ''  }} </p>
                               </p>
                    </div>
                  </div>
  
            </div>
        </div>


        <div class="card m-0 mt-3 pb-3">
            <div class="card-header bg-primary">
                <div class="h5">Audit Information</div>
            </div>
            <div class="card-body ">
                <div class="row no-gutters">
                    <div class="col-3">
                        <p><b>Created by:</b> 
                            {{ $pledge->created_by->name }} </p>
                    </div>
                    <div class="col-3">
                        <p><b>Created at:</b> 
                            {{ date_timezone_set($pledge->created_at, timezone_open('America/Vancouver')) }}
                               </p>
                    </div>
                  </div>
  
                <div class="row">
                  <div class="col-3">
                    <p><b>Modified by:</b>  
                        {{ isset($pledge->updated_by) ? $pledge->updated_by->name : ''}} </p>
                  </div>
                  <div class="col-3">
                    <p><b>Modified at:</b>  
                        {{ date_timezone_set($pledge->updated_at, timezone_open('America/Vancouver')) }} </p>
                  </div>
                </div>

                @if ($pledge->cancelled)
                    <div class="row">
                        <div class="col-3">
                        <p><b>Cancelled by:</b>
                            {{ isset($pledge->cancelled_by) ? $pledge->updated_by->name : ''}} </p>
                        </div>
                        <div class="col-3">
                        <p><b>Cancelled at:</b> 
                            {{ $pledge->cancelled_at ? $pledge->cancelled_at : ''  }} </p>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    
  </div>
</div>
@endsection




