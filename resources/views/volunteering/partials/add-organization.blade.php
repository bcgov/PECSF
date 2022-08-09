<tr class="organization" id="organization{{$index}}">
    <td>
        <div class="form-row raised">
            <div class="form-group col-md-8 charity">
                <label for="event_type">Organization Name:</label>
               <!--<input class="form-control" type="text" id="organization_name" name="organization_name[]"/>-->
                <div>
                    <input type="text" disabled class="form-control errors organization_name" name="id[]"  placeholder="" />

                    {{--
                        <form action="{{route('donate.save.select')}}" method="post">
                            @csrf
                            <label class="w-100">
                                <span class="text-muted">Search for your CRA Charity</span>
                                <select class="form-control" placeholder="type in charity name">
                                </select>
                                <small class="text-danger">
                                    {{ $errors->first('id') }}
                                </small>
                            </label>
                            <div id="selected-charity-list">

                            </div>
                            <div class="mt-2">
                                <button class="btn btn-lg btn-outline-primary">Cancel</button>
                                <button class="btn btn-lg btn-primary" type="submit">Next</button>
                            </div>
                        </form>
                    --}}
                </div>
                <span class="organization_name_errors  errors">
                       @error('organization_name.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

            </div>
@if (!Request::is('donate/select'))
            <div class="form-group col-md-4">
                <label for="sub_type">Donation Percent (%)</label>
                <input class="form-control" type="text" id="donation_percent" name="donation_percent[]">
                <span class="donation_percent_errors  errors">
                       @error('donation_percent.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>
            @endif
            <div class="form-group col-md-12">
                <label for="sub_type">Specific Community Or Initiative (Optional):</label>
                <input class="form-control" type="text" id="specific_community_or_initiative" name="additional[]" />
                <span class="specific_community_or_initiative_errors  errors">
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

