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

<tr style="display:none;" id="{{$pledge->id}}_pledge_hook_audit" class="full-row">
    <td colspan="4">
        <div class="container pt-3">
            <div class="row">
                <div class="col">
                    <b>Created by : </b>
                    {{ $pledge->created_by->name }}
                </div>
                <div class="col">
                    <b>Created at : </b>
                    {{ $pledge->created_at }}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Updated by : </b>
                    {{ $pledge->updated_by->name  }}
                </div>
                <div class="col">
                    <b>Updated at : </b>
                    {{ $pledge->updated_at }}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Approved by : </b>
                    {{ $pledge->approved_by->name  }}
                </div>
                <div class="col">
                    <b>Approved at : </b>
                    {{ $pledge->approved_at }}
                </div>
            </div>
        </div>
    </td>
</tr>