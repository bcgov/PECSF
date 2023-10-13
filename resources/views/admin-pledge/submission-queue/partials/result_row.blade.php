<tr>
    <td>{{$submission->bank_deposit_form_id}}</td>
    <td>{{$submission->event_type}}</td>
    {{-- <td>{{$submission->form_submitter_id}}</td> --}}
    <td>{{$submission->name}}</td>
    <td>{{$submission->deposit_amount}}</td>
    <td>{{$submission->organization_code}}</td>
    <td>{{$submission->pecsf_id}}</td>
    <td>{{$submission->bc_gov_id}}</td>
    <td>{{$submission->business_unit ? $submission->bu->name : ''}}</td>
    <td>{{$submission->deptid}}</td>
    <td>{{$submission->dept_name}}</td>
    <td><select class="status status{{$submission->bank_deposit_form_id}}" value="{{$submission->approved}}"  submission_id="{{$submission->bank_deposit_form_id}}"><option value="0" {{$submission->approved == 0 ? "selected" : ""}}>Pending</option><option value="1" {{$submission->approved == 1 ? "selected" : ""}}>Approved</option><option value="2" {{$submission->approved == 2 ? "selected" : ""}}>Locked</option></select></td>
    <td class="edit-event-modal" form-id="{{$submission->bank_deposit_form_id}}"><button role="button" class="btn btn-primary">View Details</button></td>
</tr>

