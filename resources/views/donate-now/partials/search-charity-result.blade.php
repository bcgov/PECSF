@if($charities)
    <h5 id="charity_count" class="noresults" style="width:100%;text-align:left;">{{$charities->total()}} results</h5>
@else
    <h5 id="charity_count" class="noresults" style="width:100%;text-align:center" class="align-content-center">No results</h5>
@endif

<table id="charities">
    @foreach($charities as $charity)
        <tr >
            <td style="width:70%;"><b>{{$charity->charity_name}}</b><br>{{$charity::CATEGORY_LIST[$charity->category_code]}} | {{$charity->city}} | {{$charity->province}} | {{$charity->country}}</td>
            <td class="blue" style="width:15%"><span class="view_details" registration_number="{{$charity->registration_number}}" charity_status="{{$charity->charity_status}}" effective_date_of_status="{{$charity->effective_date_of_status}}" sanction="{{$charity->sanction}}" designation="{{$charity->designation}}" category="{{$charity->category}}" address="{{$charity->address}}" city="{{$charity->city}}" province="{{$charity->province}}" country="{{$charity->country}}" postal="{{$charity->postal_code}}" website="{{$charity->uri}}" charitable_programs=""><b><u>View Details</u></b></span></td>
            <td style="width:5%"><div style="width:100px;" class="select btn btn-outline-primary" name="{{$charity->charity_name}}" org_id="{{$charity->id}}">Select</div></td>
        </tr>
    @endforeach
</table>
    
<div class="d-flex justify-content-center pt-3">
    {!! $charities->links() !!}
</div>

