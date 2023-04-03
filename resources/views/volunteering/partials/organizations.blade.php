<h4 class="blue text-primary" style="padding-left:8px;">Search Results</h4>
@if($organizations)
    <h5 id="charity_count" class="noresults pl-2" style="width:100%;text-align:left;">{{$organizations->total()}} results</h5>
@else
    <h5 id="charity_count" class="noresults pl-2" style="width:100%;text-align:center" class="align-content-center">No results</h5>
@endif

<div style="color:#FFF;border-bottom: #fcc642 2px solid;padding:15px;"></div>
<table id="charities" class="col-md-12">
    @foreach($organizations as $key => $organization)


        <tr>
            <td style="width:60%;"><b>{{$organization->charity_name}}</b><br>{{$organization::CATEGORY_LIST[(!empty($organization->category_code) && in_array($organization->category_code,$organization::CATEGORY_LIST))]}} | {{$organization->city}} | {{$organization->province}} | {{$organization->country}}</td>
            <td class="blue" style="width:20%"><span class="text-primary view_details" pool_description="{{$organization->description}}" pool_image="{{$organization->image}}" charity_type="{{$organization->charity_type}}" charity_name="{{$organization->charity_name}}" charity_description="" registration_number="{{$organization->registration_number}}" charity_status="{{$organization->charity_status}}" effective_date_of_status="{{$organization->effective_date_of_status}}" sanction="{{$organization->sanction}}" designation="{{$organization->designation}}" category="{{$organization->category}}" address="{{$organization->address}}" city="{{$organization->city}}" province="{{$organization->province}}" country="{{$organization->country}}" postal="{{$organization->postal_code}}" website="{{$organization->uri}}" charitable_programs=""><b><u>View Details</u></b></span></td>
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
          btn btn-outline-primary form-control" name="{{$organization->charity_name}}" org_id="{{$organization->id}}">
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


<div class="col-md-12">
    @if($organizations)
        {{$organizations->links()}}
    @else

    @endif
</div>

<br>

