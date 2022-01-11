
@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif

<div class="card">
    
    <div class="d-flex mt-3">
        <h4></h4>
        <div class="px-4">
            <form action="{{ route('campaignyears.index') }}" class="form-inline" method="get">
                <div class="form-group mx-sm-3 mb-2">
                  <input type="text" class="form-control" name="q" placeholder="Year" 
                    value="{{ $search }}">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Search</button>
              </form>

        </div>    
        <div class="flex-fill"></div>
        <div class="px-4">
            <x-button :href="route('campaignyears.create')">Add a New Value</x-button>        
        </div>
    </div>


    <div class="card-body">
                <table class="table table-sm table-bordered rounded">
                    <thead>
                    <tr class="text-center bg-light">
                        <th>Calendar Years</th>
                        <th>Status</th>
                        <th>Number of Period</th>
                        <th width="280px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($campaign_years as $campaign_year)
                     
                            <tr class="text-center">
                                <td class="text-left">{{$campaign_year->calendar_year}} </td>
                                <td>{{$campaign_year->status == 'A' ? 'Active' : 'Inactive'}}</td>
                                <td>{{$campaign_year->number_of_periods}}</td>

                                <!--
                                <td>{{$campaign_year->start_date->format('F j, Y') }}</td>
                                <td>{{$campaign_year->end_date->format('F j, Y') }}</td>
                                <td>{{$campaign_year->close_date->format('F j, Y') }}</td>
                                -->
                                <td>
                                    <a class="btn btn-info btn-sm"" href="{{ route('campaignyears.show',$campaign_year->id) }}">Show</a>
                    
                                    <a class="btn btn-primary btn-sm"" href="{{ route('campaignyears.edit',$campaign_year->id) }}">Edit</a>
                                </td>
                            </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col">
                    <span class="float-left px-2">
                        Showing {{ $campaign_years->firstItem() }}–{{ $campaign_years->lastItem() }} of {{ $campaign_years->total() }} results
                        </span>
                </div>
                <div class="col">
                </div>
                <div class="col">
                    <span class="pr-4 float-right">
                        {{  $campaign_years->withQueryString()->links('pagination::bootstrap-4')  }}                
                    </span>
                </div>
            </div>
    </div>


  
    <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 2000);
    </script>