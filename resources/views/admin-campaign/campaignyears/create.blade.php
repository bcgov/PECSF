@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <h4 class="mx-1 mt-3">{{ isset($campaign_year) ? 'Edit ' : 'Create ' }} Campaign Year</h4>

    <div class="mx-1 pt-2">
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('settings.campaignyears.index') }}'">
            Back    
        </button> 
    </div>

@endsection
@section('content')
<div class="card">
    <div class="card-body">

        <form action="{{ isset($campaign_year) ? route('settings.campaignyears.update', $campaign_year->id ) : route('settings.campaignyears.store') }}" 
            method="post">
            @csrf
            @if(isset($campaign_year))
                @method('PUT')
            @endif
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="calendar_year">Calendar Year</label>
                @if(isset($campaign_year))
                    <input type="text" class="form-control" id="number_of_periods" name="calendar_year" value="{{ $campaign_year->calendar_year }}" readonly>
                @else
                    <select id="calendar_year" class="form-control @error('calendar_year') is-invalid @enderror" name="calendar_year">
                        @for ($year = 1980; $year <= 2099 ; $year++)
                            <option value="{{ $year }}" {{ $year == (old('calendar_year') ? old('calendar_year') : date('Y') + 1 ) ? 'selected' : '' }}>
                                {{ $year }} 
                            </option>
                        @endfor
                    </select>  
                    @error('calendar_year')
                    <span class="invalid-feedback">
                    {{  $message  }}
                    </span>
                @enderror
                @endif
              </div>

              <div class="form-group col-md-4">
                <label for="number_of_periods">Pay Periods for Year</label>
                <input type="text" class="form-control @error('number_of_periods') is-invalid @enderror" id="number_of_periods" name="number_of_periods" 
                @if(isset($campaign_year))    
                    @error('number_of_periods') 
                        value="{{ old('number_of_periods') }}">
                    @else
                        value="{{ old('number_of_periods') ? old('number_of_periods') : $campaign_year->number_of_periods }}">
                    @enderror
                @else 
                    value="{{ old('number_of_periods') }}">
                @endif
                @error('number_of_periods')
                    <span class="invalid-feedback">
                    {{  $message  }}
                    </span>
                @enderror
              </div>
              <div class="form-group col-md-4">
                <label for="status">Status</label>
                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status">
                    @if(isset($campaign_year))  
                        @error('status')
                            {{ $val_status = old('status')  }}    
                        @else
                            {{ $val_status = old('status') ? old('status') : $campaign_year->status }}    
                        @enderror
                    @else
                        {{ $val_status = old('status') ? old('status') : 'A' }}    
                    @endif
                    <option value="A" {{ $val_status == 'A' ? 'selected' : '' }}>{{ 'Active'   }}</option>
                    <option value="I" {{ $val_status == 'I' ? 'selected' : '' }}>{{ 'Inactive' }}</option>
                </select>
                @error('status')
                    <span class="invalid-feedback">
                    {{  $message  }}
                    </span>
                @enderror
              </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="start_date">Campaign Start Date</label>
                    <input type="date" class="form-control  @error('start_date') is-invalid @enderror" name="start_date" 
                    @if(isset($campaign_year)) 
                        @error('start_date')   
                            value="{{ old('start_date') }}">
                        @else
                            value="{{ old('start_date') ? old('start_date') : $campaign_year->start_date->toDateString() }}">
                        @enderror
                    @else 
                        value="{{ old('start_date') }}">
                    @endif
                    @error('start_date')
                    <div class="invalid-feedback">
                        {{  $message }}
                    </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="end_date">Campaign End Date</label>
                    <input type="date" class="form-control  @error('end_date') is-invalid @enderror" name="end_date" 
                    @if(isset($campaign_year))  
                        @error('end_date')     
                            value="{{ old('end_date') }}">
                        @else
                            value="{{ old('end_date') ? old('end_date') : $campaign_year->end_date->toDateString() }}">
                        @enderror
                    @else 
                        value="{{ old('end_date') }}">
                    @endif
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
                        <input type="date" class="form-control  @error('close_date') is-invalid @enderror" id="close_date" name="close_date" 
                    @if(isset($campaign_year)) 
                        @error('close_date')        
                            value="{{ old('close_date') }}">
                        @else
                            value="{{ old('close_date') ? old('close_date') : $campaign_year->close_date->toDateString() }}">
                        @enderror
                    @else 
                        value="{{ old('close_date') ? old('close_date') : (date('Y') + 1) .'-12-31' }}">
                    @endif
                    @error('close_date')
                    <div class="invalid-feedback">
                        {{  $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div>
                <button type="submit" class="btn btn-primary">Save</button>
                </div>
                <div class="pl-2">
                    <a href="{{ route('settings.campaignyears.index') }}"> 
                    <button type="button" class="btn btn-secondary ">Cancel</button>
                    </a>
                </div>
            </div>
          </form>

@endsection

@push('js')
<script>
    $(function() {

        $('#calendar_year').change(function(e) {
              $('#close_date').val( e.target.value + '-12-31');
        });

    });
</script>

@endpush