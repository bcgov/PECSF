<tr class="organization" id="organization{{$index}}">
    <td>
        <div class="form-row mx-3 raised">
            <div class="form-group col-md-8 charity pt-2">
                <label for="event_type">Organization Name:</label>
               <!--<input class="form-control" type="text" id="organization_name" name="organization_name[]"/>-->
                <div>

                    <input type="text" disabled class="form-control errors organization_name" name="organization_name[]" value="{{(($charity != "YYY") ? $charity->organization_name : "Disabled")}}" placeholder="" />
                    <input type="hidden" name="vendor_id[]" value="{{(($charity != "YYY") ? $charity->vendor_id : "")}}"/>
                    <input type="hidden" name="id[]" value="{{(($charity != "YYY") ? $charity->vendor_id : "")}}"/>
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
            <div class="form-group col-md-2 pt-2">
                <label for="sub_type">Donation Percent (%)</label>
                <input class="form-control" type="text" id="donation_percent" name="donation_percent[]" value="{{ ($charity != "YYY") ? $charity->donation_percent : ''}}">
                <span class="donation_percent_errors  errors">
                       @error('donation_percent.'.$index)
                        <span class="invalid-feedback">{{  $message  }} </span>
                    @enderror
                  </span>
            </div>
            @endif

            <div class="form-group col-md-1 pt-2">
                <label class="d-block" for="">&nbsp</label>
                <div class="float-right">
                    <button class="btn btn-danger remove">
                        {{--    <i class="fa fa-trash"></i> --}}
                        Remove</button>
                </div>
            </div>


            <div class="form-group col-md-10">
                <label for="sub_type">Specific Community Or Initiative (Optional):</label>
                <input class="form-control specific_community_or_initiative" type="text" id="specific_community_or_initiative" name="additional[]" 
                                    value="{{ ($charity != "YYY") ? $charity->specific_community_or_initiative : ''}}" />
                <span class="specific_community_or_initiative_errors  errors">
                       @error('specific_community_or_initiative.'.$index)
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
            </div>
            {{-- <div class="form-group col-md-12">
                <button class="btn btn-danger remove">Remove</button>
            </div> --}}
        </div>
    </td>
</tr>

