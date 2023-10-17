<tr class="attachment" id="attachment{{$index}}">
    <td><span class="filename"></span></td>
    <td><u><a target="_blank" class="p-2 text-primary view_attachment">Download</a></u> {{---- | <u><a attr-index="attachment{{$index}}" class="p-2 text-primary delete_attachment">Delete</a></u> ---}} </td>
    <td>
        <span class="attachment_errors errors">
            @error('attachment.'.$index)
                <span class="invalid-feedback">{{  $message  }}</span>
            @enderror
        </span>
    </td>
</tr>
