@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <div class="d-flex mt-3">
        <h4>PECSF Campaign Year</h4>
        <div class="flex-fill"></div>       
    </div>
@endsection
@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{route('settings.campaignyears.store')}}" method="post">
            @csrf
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="calendar_year">Calendar Year</label>
                <input type="text" class="form-control" id="number_of_periods" name="number_of_periods" value="{{ $campaign_year->calendar_year }}" readonly>
              </div>
              <div class="form-group col-md-4">
                <label for="number_of_periods">Pay Periods for Year</label>
                <input type="text" class="form-control @error('number_of_periods') is-invalid @enderror" id="number_of_periods" name="number_of_periods" value="{{ $campaign_year->number_of_periods }}" readonly>
              </div>
              <div class="form-group col-md-4">
                <label for="status">Status</label>
                <input type="text" class="form-control" id="number_of_periods" name="number_of_periods" 
                  value="{{ $campaign_year->status == 'A' ? 'Active' : 'Inactive' }}" readonly>
              </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="start_date">Campaign Start Date</label>
                    <input type="text" class="form-control  @error('start_date') is-invalid @enderror" name="start_date" value="{{ $campaign_year->start_date->toDateString() }}" readonly>
                    @error('start_date')
                    <div class="invalid-feedback">
                        {{  $message }}
                    </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="end_date">Campaign End Date</label>
                    <input type="text" class="form-control  @error('end_date') is-invalid @enderror" name="end_date" value="{{ $campaign_year->end_date->toDateString() }}" readonly>
                    @error('end_date')
                    <div class="invalid-feedback">
                        {{  $message }}
                    </div>
                    @enderror
                </div>
                
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="close_date">Campaign Closed  Date</label>
                    <input type="text" class="form-control  @error('close_date') is-invalid @enderror" name="close_date" value="{{ $campaign_year->close_date->toDateString() }}" readonly>
                    @error('close_date')
                    <div class="invalid-feedback">
                        {{  $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="container  my-3">
                <div class="row no-gutters my-3">
                    <div class="border-bottom col-8">
                        <h5>Audit Information</h5>
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="col-3">
                        <p>Created by: 
                            {{ $campaign_year->created_by->name }} </p>
                    </div>
                    <div class="col-3">
                        <p>Created at: 
                            {{ date_timezone_set($campaign_year->created_at, timezone_open('America/Vancouver')) }}
                               </p>
                    </div>
                  </div>
  
                <div class="row">
                  <div class="col-3">
                    <p>Modified by: 
                        {{ isset($campaign_year->modified_by) ? $campaign_year->modified_by->name : ''}} </p>
                  </div>
                  <div class="col-3">
                    <p>Modified at: 
                        {{ isset($campaign_year->modified_by) ? date_timezone_set($campaign_year->updated_at, timezone_open('America/Vancouver')) 
                           : '' }} </p>
                  </div>
                </div>
        
              </div>

            <div class="form-row">
                <a href="{{ route('settings.campaignyears.index') }}"> 
                   <button type="button" class="btn btn-primary float-right ">back</button>
                   </a>
                </div>
            </div>
          </form>

@endsection