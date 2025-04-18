<h4 class="text-primary pl-4">Search Results</h4>
@if($organizations)
    <div id="charity_count" class="noresults text-secondary pl-4" style="width:100%;text-align:left;"><b>{{ number_format($organizations->total(),0) }} results</b></div>
@endif

<div class="pb-2" style="color:#FFF;border-bottom: #fcc642 2px solid;"></div>
@if(!($organizations))
    <h3 id="charity_count" class="noresults pl-4 pt-2" style="width:100%;text-align:center" class="align-content-center">No results</h3>
@endif

<table id="charities" class="col-md-12">
    @foreach($organizations as $key => $organization)

        <tr>
            <td style="width:60%;"><b>{{$organization->charity_name}}</b><br>{{$organization::CATEGORY_LIST[(!empty($organization->category_code) && in_array($organization->category_code,$organization::CATEGORY_LIST))]}} | {{$organization->city}} | {{$organization->province}} | {{$organization->country}}</td>
            <td class="blue" style="width:20%"><button type="button" class="btn btn-link text-primary view_details" pool_description="{{$organization->pool_description}}" pool_image="/img/uploads/fspools/{{$organization->image}}" charity_type="{{$organization->charity_type}}" employee_name="{{$organization->employee_name}}" charity_name="{{$organization->charity_name}}" charity_description="" registration_number="{{$organization->registration_number}}" charity_status="{{$organization->charity_status}}" effective_date_of_status="{{$organization->effective_date_of_status}}" sanction="{{$organization->sanction}}" designation="{{$organization->designation}}" category="{{$organization->category}}" address="{{$organization->address}}" city="{{$organization->city}}" province="{{$organization->province}}" country="{{$organization->country}}" postal="{{$organization->postal_code}}" website="{{$organization->uri}}" charitable_programs="{{$organization->program_name}}"><b><u>View Details</u></b></button></td>
            <td style="width:20%"><button style="" type="button" class="
            @php
            if(in_array($organization->id,$selected_vendors))
                {
                    echo "selected active ";
                }
            else{
                echo "select ";
            }
            @endphp
          btn btn-outline-primary form-control" name="{{$organization->charity_name}}" org_id="{{$organization->id}}" program_name="{{$organization->program_name}}">
                    @php
                    if(in_array($organization->id,$selected_vendors))
                    {
                    echo "Selected";
                    }
                    else{
                    echo "Select";
                    }
                    @endphp</button></td>
        </tr>
    @endforeach

</table>
<div class="p-0">
    @if($organizations)
        {{$organizations->onEachSide(1)->links('volunteering.partials.pagination')}}
    @else

    @endif
</div>
<br>

