
@if($organizations)
    <h5 id="charity_count" class="noresults" style="width:100%;text-align:left;">{{$organizations->total()}} results</h5>
@else
    <h5 id="charity_count" class="noresults" style="width:100%;text-align:center" class="align-content-center">No results</h5>
@endif

<table id="charities">
    @foreach($organizations as $organization)
        <tr >
            <td style="width:70%;"><b>{{$organization->charity_name}}</b><br>{{$organization::CATEGORY_LIST[$organization->category_code]}} | {{$organization->city}} | {{$organization->province}} | {{$organization->country}}</td>
            <td class="blue" style="width:15%"><span class="view_details" registration_number="{{$organization->registration_number}}" charity_status="{{$organization->charity_status}}" effective_date_of_status="{{$organization->effective_date_of_status}}" sanction="{{$organization->sanction}}" designation="{{$organization->designation}}" category="{{$organization->category}}" address="{{$organization->address}}" city="{{$organization->city}}" province="{{$organization->province}}" country="{{$organization->country}}" postal="{{$organization->postal_code}}" website="{{$organization->uri}}" charitable_programs=""><b><u>View Details</u></b></span></td>
            <td style="width:5%"><div style="width:100px;" class="select btn btn-outline-primary" name="{{$organization->charity_name}}" org_id="{{$organization->id}}">Select</div></td>
        </tr>
    @endforeach
</table>

