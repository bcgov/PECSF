<tr>
    <td>{{$submission->event_type}}</td>
    <td>{{$submission->form_submitter_id}}</td>
    <td>{{$submission->name}}</td>
    <td>{{$submission->deposit_amount}}</td>
    <td>{{$submission->organization_code}}</td>
    <td><select><option>Status</option></select></td>
    <td class="edit-event-modal" form-id="{{$submission->id}}">View Details</td>
</tr>

