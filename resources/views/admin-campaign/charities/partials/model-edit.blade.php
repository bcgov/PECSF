{{-- Modal for --}}
<div class="modal fade" id="charity-edit-modal" tabindex="-1" role="dialog" aria-labelledby="charityModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="charityModalLabel">Edit an existing charity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="charity-edit-model-form">
                    <input type="hidden" class="form-control" name="id" value="" disabled>

                    <div class="form-group row  m-0">
                        <label for="charity_name" class="col-sm-3 col-form-label col-form-label">Organization
                            Name:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control" id="charity_name" name="charity_name"
                                disabled>
                        </div>
                    </div>

                    <div class="form-group row  m-0">
                        <label for="registration_number" class="col-sm-3 col-form-label col-form-label">Business Number:
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control" id="registration_number"
                                name="registration_number" disabled>
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="charity_status" class="col-sm-3 col-form-label col-form-label">Status:
                            </label>
                        <div class="col-sm-4">
                            {{-- <input type="text" class="form-control form-control" name="charity_status" value="" disabled> --}}
                            <select id="charity_status" class="form-control" name="charity_status">
                                @foreach ($charity_status_list as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach 
                            </select>
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="effdt" class="col-sm-3 col-form-label col-form-label">Effective Date:
                            </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control" name="effdt" value="" disabled>
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="designation_code" class="col-sm-3 col-form-label col-form-label">Designation:
                            </label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control form-control" name="designation_code" value="" disabled>
                        </div>
                            <div class="col-sm-8">
                            <input type="text" class="form-control form-control" name="designation_name" value="" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group row m-0">
                        <label for="category_code" class="col-sm-3 col-form-label col-form-label">Category:
                            </label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control form-control" name="category_code" value="" disabled>
                        </div>
                            <div class="col-sm-8">
                            <input type="text" class="form-control form-control" name="category_name" value="" disabled>
                            
                        </div>
                    </div>

                    <h6 class="text-primary font-weight-bold mt-2">Original Mailing Address Information</h6>
                    <div class="card pb-0">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="address" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city" value="" disabled>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="inputCity">Province</label>
                                    <input type="text" class="form-control" name="province" value="" disabled>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="inputState">Postal Code</label>
                                    <input type="text" class="form-control" name="postal_code" value="" disabled>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="inputZip">Country</label>
                                    <input type="text" class="form-control" name="country" value="" disabled>
                                </div>
                            </div>
                        </div>
                    </div>


                    <h6 class="text-primary font-weight-bold">Override Mailing Address Information</h6>
                    <div class="card">
                        <div class="card-body">

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="use_alt_address"
                                        name="use_alt_address" value="Yes">
                                    <label class="form-check-label font-weight-bold" for="use_alt_address">
                                        &nbsp;&nbsp;Use Alternative Address
                                    </label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="alt_address1">Address 1</label>
                                    <input type="text" class="form-control" id="alt_address1" name="alt_address1">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="alt_address2">Address 2</label>
                                    <input type="text" class="form-control" id="alt_address2" name="alt_address2">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="alt_city">City</label>
                                    <input type="text" class="form-control" id="alt_city" name="alt_city">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="alt_province">Province</label>
                                    <input type="text" class="form-control" id="alt_province" name="alt_province">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="alt_postal_code">Postal Code</label>
                                    <input type="text" class="form-control" id="alt_postal_code"
                                        name="alt_postal_code">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="alt_country">Country</label>
                                    <input type="text" class="form-control" id="alt_country" name="alt_country">

                                </div>
                            </div>
                        </div>
                    </div>


                    <h6 class="text-primary font-weight-bold">Charity Financial Contact Information</h6>
                    <div class="card">
                        <div class="card-body">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="financial_contact_name">Charity Financial Contact Name</label>
                                    <input type="text" class="form-control" id="financial_contact_name"
                                        name="financial_contact_name">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="financial_contact_title">Charity Financial Contact Title</label>
                                    <input type="text" class="form-control" id="financial_contact_title"
                                        name="financial_contact_title">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="financial_contact_email">Charity Financial Email</label>
                                    <input type="text" class="form-control" id="financial_contact_email"
                                        name="financial_contact_email">
                                </div>
                            </div>


                        </div>
                    </div>

                    <h6 class="text-primary font-weight-bold">Notes</h6>
                    <div class="card">
                        <div class="card-body">

                            <div class="form-row">

                                <div class="form-group col-md-12">
                                    <textarea class="form-control" id="comments" name="comments" rows="5"></textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>


            </div>
            <div class="modal-footer">
                <button type="button" id="save-confirm-btn" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
