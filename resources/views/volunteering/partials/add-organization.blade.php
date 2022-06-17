<tr class="organization" id="organization{{$index}}">
    <td>
        <div class="form-row raised">
            <div class="form-group col-md-4">
                <label for="event_type">Organization Name:</label>
                <input class="form-control" type="text" id="organization_name" name="organization_name[]"/>

                <span class="organization_name_errors">
                       @error('organization_name.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

            </div>
            <div class="form-group col-md-4">
                <label for="region">Vendor ID:</label>
                <input class="form-control" type="text" id="vendor_id" name="vendor_id[]" />
                <span class="vendor_id_errors">
                       @error('vendor_id.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

            </div>
            <div class="form-group col-md-4">
                <label for="sub_type">Donation Percent:</label>
                <input class="form-control" type="text" id="donation_percent" name="donation_percent[]">
                <span class="donation_percent">
                       @error('donation_percent.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

            </div>
            <div class="form-group col-md-12">
                <label for="sub_type">Specific Community Or Initiative:</label>
                <input class="form-control" type="text" id="specific_community_or_initiative" name="specific_community_or_initiative[]" />
                <span class="specific_community_or_initiative">
                       @error('specific_community_or_initiative.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>
            <div class="form-group col-md-12">
                <button class="btn btn-danger remove">Remove</button>
            </div>
        </div>
    </td>
</tr>

