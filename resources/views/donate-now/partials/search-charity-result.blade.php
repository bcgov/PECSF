<h4 style="padding-left:8px;">Search Results</h4>
@if($charities)
    <p id="charity_count" class="noresults pl-2" style="width:100%;text-align:left;"><b>{{$charities->total()}} results</b></p>
@else
    <h3 id="charity_count" class="noresults pl-2" style="width:100%;text-align:center" class="align-content-center">No results</h3>
@endif

<table id="charities">
    <tbody>
    @foreach($charities as $key => $charity)
        <tr>
            <td style="width:70%;"><b>{{$charity->charity_name}}</b><br>{{$charity::CATEGORY_LIST[$charity->category_code]}} | {{$charity->city}} | {{$charity->province}} | {{$charity->country}}</td>
            <td class="blue" style="width:15%" ><span class=" view_details" registration_number="{{$charity->registration_number}}" charity_status="{{$charity->charity_status}}" effective_date_of_status="{{$charity->effective_date_of_status}}" sanction="{{$charity->sanction}}" designation="{{$charity->designation}}" category="{{$charity->category}}" address="{{$charity->address}}" city="{{$charity->city}}" province="{{$charity->province}}" country="{{$charity->country}}" postal="{{$charity->postal_code}}" website="{{$charity->uri}}" charitable_programs=""><b><u>View Details</u></b></span></td>
            <td style="width:5%"><button style="width:100px;" class="select-btn btn btn-outline-primary {{ $selected_charity_id == $charity->id ? 'active' : '' }}"
                    name="{{$charity->charity_name}}" org_id="{{$charity->id}}">
                        {{  $selected_charity_id == $charity->id ? 'Selected' : 'Select' }}</button></td>
        </tr>
    @endforeach
    </tbody>
</table>
<br>
<div class="d-flex justify-content-center pt-3">
    {!! $charities->links() !!}
</div>

