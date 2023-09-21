<tr class="organization" id="organization{{$index}}">
    <td>
        <div class="form-row mx-3">
            <div class="form-group col-md-8 charity">
                <label for="event_type">Organization Name:</label>
               <!--<input class="form-control" type="text" id="organization_name" name="organization_name[]"/>-->
                <div>
                    <input type="text" disabled class="form-control errors organization_name" name="organization_name[]" value="{{(($charity != "YYY") ? $charity->text : "Disabled")}}" placeholder="" />
                    <input type="hidden" name="vendor_id[]" value="{{(($charity != "YYY") ? $charity->id : "")}}"/>
                    {{-- <input type="hidden" name="id[]" value="{{(($charity != "YYY") ? $charity->id : "")}}"/> --}}
                    <input type="hidden" name="charities[]" value="{{(($charity != "YYY") ? $charity->id : "")}}"/>
                    
                </div>
                <span class="organization_name_errors  errors">
                       @error('organization_name.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

            </div>

            <div class="form-group col-md-4">
                <label class="d-block" for="">&nbsp</label>
                <div class="float-right">
                    <button class="btn btn-danger remove">
                        {{--    <i class="fa fa-trash"></i> --}}
                        Remove</button>
                </div>
            </div>

            <div class="form-group col-md-12">
                <label for="sub_type">Specific Community Or Initiative (Optional): </label>
                <input class="form-control specific_community_or_initiative" type="text" id="specific_community_or_initiative" name="additional[]" value="{{ ($charity != 'YYY') ? $charity->program_name : '' }}" readonly />
                <span class="specific_community_or_initiative_errors  errors">
                       @error('specific_community_or_initiative.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>
            
        </div>
    </td>
</tr>

