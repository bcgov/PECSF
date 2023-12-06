<tr class="attachment" id="attachment{{$index}}">
    <td>
        <div class = "row">
            <div class = "col-12 col-lg-6 col-md-8"><span class="filename"></span></div>
            <div class = "col-12 col-lg-6 col-md-4"><a target="_blank" class="p-2 text-primary view_attachment">Download</a></div>
            {{-- <u><a attr-index="attachment{{$index}}" class="p-2 text-primary delete_attachment">Delete</a></u> - --}}
         </div>    
    </td>
    <td>
        <span class="attachment_errors errors">
            @error('attachment.'.$index)
                <span class="invalid-feedback">{{  $message  }}</span>
            @enderror
        </span>
    </td>
</tr>
