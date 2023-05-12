<tr>
    <td>{{$pledge->organization_code}}</td>
    <td>{{$pledge->name}}</td>

    <td>{{$pledge->organization_code == "GOV" ? $pledge->bc_gov_id : ""}}</td>
    <td>{{ !empty($pledge->pecsf_id) ? $pledge->pecsf_id : ""}}</td>
    <td>{{$pledge->campaign_year ? $pledge->campaign_year->calendar_year  : null }}</td>
    <td>{{$pledge->event_type}}</td>
    <td>{{number_format($pledge->deposit_amount,2)}}</td>
    <td>{{$pledge->sub_type}}</td>
    <th><a href="#" class="more-info-pledge fas fa-info-circle fa-2x bottom-right" data-id="{{$pledge->id}}_pledge_hook"></a></th>
</tr>

<tr style="display:none;" id="{{$pledge->id}}_pledge_hook" class="full-row">
    <td>{{$pledge->deposit_date}}</td>
    <td>{{number_format($pledge->deposit_amount,2)}}</td>
    <td>{{$pledge->description}}</td>
    <td>{{$pledge->employment_city}}</td>
</tr>
