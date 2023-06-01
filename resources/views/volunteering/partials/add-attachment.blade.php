<tr class="attachment" id="attachment{{$index}}">
    <td><span class="filename"></span></td>
    <td><u><a target="_blank" class="p-2 text-primary view_attachment">Download</a></u></td>
    <td>
        <span class="attachment_errors errors">
            @error('attachment.'.$index)
                <span class="invalid-feedback">{{  $message  }}</span>
            @enderror
        </span>
    </td>
</tr>
