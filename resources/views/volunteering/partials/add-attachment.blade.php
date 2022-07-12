<tr class="attachment" id="attachment{{$index}}">
    <td>
        <span class="attachment_errors errors">
            @error('attachment.'.$index)
                <span class="invalid-feedback">{{  $message  }}</span>
            @enderror
        </span>
    </td>
    <td><span class="filename"></span></td>
    <td><label class="btn btn-primary" for="attachment_input_{{$index}}"><input style="display:none" id="attachment_input_{{$index}}" name="attachments[]" type="file" />Add</label></td>
    <td><button class="btn btn-primary view_attachment">View</button></td>
    <td><button class="btn btn-danger remove_attachment">Delete</button></td>
    <td><i class="fas fa-minus remove"></i></td>
</tr>
