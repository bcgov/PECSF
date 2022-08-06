@foreach($organizations as $organization)
    <tr >
        <td style="width:80%;"><b>{{$organization->charity_name}}</b><br>{{$organization::CATEGORY_LIST[$organization->category_code]}} | {{$organization->city}} | {{$organization->province}} | {{$organization->country}}</td>
        <td class="blue" style="width:9%"><b><u>View Details</u></b></td>
        <td style="width:5%"><div style="width:100px;" class="select btn btn-outline-primary" name="{{$organization->charity_name}}" org_id="{{$organization->id}}">Select</div></td>
    </tr>
@endforeach
