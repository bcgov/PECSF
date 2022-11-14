<tr>
    <td>{{$pledge->organization_code}}</td>
    <td>{{$pledge->pecsf_id ? $pledge->pecsf_id : $pledge->bc_gov_id}}</td>
    <td>{{$pledge->id}}</td>
    <td>{{$pledge->created_at}}</td>
    <td>{{$pledge->event_type}}</td>
    <td>{{$pledge->sub_type}}</td>
    <th><a href="#" class="more-info-pledge fas fa-info-circle fa-2x bottom-right" data-id="{{$pledge->id}}_pledge_hook"></a></th>
</tr>

<tr style="display:none;" id="{{$pledge->id}}_pledge_hook" class="full-row">
    <td>{{$pledge->deposit_date}}</td>
    <td>{{$pledge->deposit_amount}}</td>
    <td>{{$pledge->description}}</td>
    <td>{{$pledge->employment_city}}</td>
</tr>
