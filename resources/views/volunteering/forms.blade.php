@extends('adminlte::page')

@section('content_header')
    <div class="d-flex mt-3">
        <h1>Forms</h1>
        <div class="flex-fill"></div>
    </div>
@endsection



@section('content')

    @include('volunteering.partials.form_tabs')


    <div class="card">
        <div class="card-body">
        <h3 class="blue">PECSF Bank Deposit Form</h3>

            <form id="create_pool" action="{{ route("settings.fund-supported-pools.store") }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="organization_code">Organization Code</label>
                         <input type="text" class="form-control" id="organization_code" placeholder="">

                        <span class="organization_code_errors">
                          @error('organization_code')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                    </span>


                    </div>
                    <div class="form-group col-md-4">
                        <label for="form_submitter">Form Submitter</label>
                        <div id="form_submitter">Employee A</div>
                        <span class="start_date_errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>
                    <div class="form-group col-md-4">
                        <label for="campaign_year">Campaign Year</label>
                        <div id="campaign_year">Employee A</div>
                        <span class="campaign_year_errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>
                </div>
                    <div class="form-row">
                        <div class="form-group">
                            <h5 class="blue">Event Details</h5>
                        </div>
                    </div>

                    <div class="raised form-row">


                    <div class="form-group col-md-3">
                        <label for="event_type">Event Type:</label>
                        <select class="form-control" type="text" id="event_type" name="event_type">
                        <option></option>
                        </select>
                        <span class="event_type_errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>
                        <div class="form-group col-md-3">
                            <label for="sub_type">Sub Type:</label>
                            <select class="form-control" type="text" id="sub_type" name="sub_type">
                                <option></option>
                            </select>
                            <span class="sub_type_errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                        </div>

                        <div class="form-group col-md-3">
                            <label for="sub_type">Deposit Date:</label>
                            <input class="form-control" type="date" id="deposit_date" name="deposit_date">
                            <span class="deposit_date_errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                        </div>

                        <div class="form-group col-md-3">
                            <label for="sub_type">Deposit Amount:</label>
                            <input class="form-control" type="text" id="deposit_amount" name="deposit_amount" />

                            <span class="deposit_amount_errors">
                       @error('form_submitter')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                        </div>

                    </div>


                <div class="form-row">
                    <div class="form-group">
                        <h5 class="blue">Description</h5>
                    </div>
                </div>

<div class="form-row raised">
    <div class="form-group col-md-12">
        <input class="form-control" type="text" name="description" id="description" />
    </div>
    <span>*Include Event Name-Date (DD/MM/YYYY) - Name of Coordinator</span>
</div>

                <div class="form-row">
                    <div class="form-group">
                        <h5 class="blue">Work Location</h5>
                    </div>
                </div>
                <div class="form-row raised">

                    <div class="form-group col-md-3">
                        <label for="event_type">*Employment City:</label>

                        <input class="form-control search_icon" type="text" id="employment_city" name="employment_city"/>

                        <span class="employment_city_errors">
                       @error('employment_city')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>
                    <div class="form-group col-md-3">
                        <label for="region">*Region:</label>
                        <input class="form-control search_icon" type="text" id="region" name="region" />
                        <span class="region_errors">
                       @error('region')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>

                    <div class="form-group col-md-3">
                        <label for="sub_type">Business Unit:</label>
                        <input class="form-control search_icon" type="text" id="business_unit" name="business_unit">
                        <span class="business_unit_errors">
                       @error('business_unit')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>

                    <div class="form-group col-md-3">
                        <label for="sub_type">Department:</label>
                        <input class="form-control search_icon" type="text" id="department" name="department" />
                        <span class="department_errors">
                       @error('department')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h5 class="blue">Mailing address for charitable receipt</h5>
                    </div>
                </div>
                <div class="form-row raised">

                    <div class="form-group col-md-6">
                        <label for="event_type">Address Line 1:</label>
                        <input class="form-control" type="text" id="address_1" name="address_1"/>

                        <span class="address_1_errors">
                       @error('address_1')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>
                    <div class="form-group col-md-6">
                        <label for="region">Address Line 2:</label>
                        <input class="form-control" type="text" id="address_2" name="address_2" />
                        <span class="address_2_errors">
                       @error('region')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>

                    <div class="form-group col-md-4">
                        <label for="sub_type">City:</label>
                        <input class="form-control" type="text" id="city" name="city">
                        <span class="city_errors">
                       @error('city')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>

                    <div class="form-group col-md-4">
                        <label for="sub_type">Province:</label>
                        <input class="form-control" type="text" id="province" name="province" />
                        <span class="province_errors">
                       @error('province')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>
                    <div class="form-group col-md-4">
                        <label for="sub_type">Postal Code:</label>
                        <input class="form-control" type="text" id="postal_code" name="postal_code" />
                        <span class="postal_code_errors">
                       @error('postal_code')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>

                    </div>

                </div>

                <div class="form-row">
                    <div class="form-group">
                        <h3 class="">Charity selections and distribution</h3>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <input type="radio" id="charity_selection_1" name="charity_selection" />
                        <label class="blue" for="charity_selection_1">Fund Supported Pool</label>
                        <br>
<span style="padding:20px;">
    By choosing this option your donation will support the current Fund Supported Pool of regional programs. Click on the tiles to learn about the programs in each regional pool.
</span>
                        <span class="department_errors">
                       @error('department')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror


                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="radio" id="charity_selection_2" name="charity_selection" />
                        <label class="blue" for="charity_selection_2">Donor Choice</label>
                    </div>
                    <div class="form-group  col-md-6">
                      <a href="https://apps.cra-arc.gc.ca/ebci/hacc/srch/pub/dsplyBscSrch?request_locale=en" target="_blank"><h5 class="blue float-right">View CRA Charity List</h5></a>
                    </div>
                </div>

               <table id="organizations" style="width:100%">
                   @include('volunteering.partials.add-organization', ['index' => 0])
               </table>


                <div class="form-row">
                    <div class="form-group pointer col-md-12" id="add_row">
                       <h5 class="blue"> <i class="fas fa-plus"></i>&nbsp;Add Another Organization</h5>
                    </div>
                </div>



                <div class="form-row">
                    <div class="form-group col-md-12">
                     <h3 class="blue">Attachment</h3>
                    </div>
                </div>

                <div class="form-row raised">
                    <div class="form-group col-md-12">

                        <table class="table">
                            <thead>
                            <tr>
                                <th class="blue"></th>
                                <th class="blue">Attached File</th>
                                <th class="blue">Add Attachment</th>
                                <th class="blue">Delete Attachment</th>
                                <th class="blue">View Attachment</th>
                                <th class="blue"></th>

                            </tr>
                            </thead>
                            <tbody>
                            <tr class="attachment" id="attachment1">
                                <td>1</td>
                                <td><span class="filename"></span></td>
                                <td><label class="btn btn-primary" for="attachment_input_1"><input style="display:none" id="attachment_input_1" name="attachments[]" type="file" />Add</label></td>
                                <td></td>
                                <td><button class="btn btn-primary">View</button></td>
                                <td><i class="fas fa-plus add_attachment_row"></i></td>
                            </tr>


                            </tbody>
                        </table>

                    </div>
                </div>


            </form>

        </div>
    </div>
    @push('css')


        <style>
            .select2 {
                width:100% !important;
            }
            .select2-selection--multiple{
                overflow: hidden !important;
                height: auto !important;
                min-height: 38px !important;
            }

            .select2-container .select2-selection--single {
                height: 38px !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 38px !important;
            }

            table tr{
                background:#fff;
            }

        </style>

    @endpush


    @push('js')

        <script type="x-tmpl" id="organization-tmpl">
            @include('volunteering.partials.add-organization', ['index' => 'XXX'] )
        </script>

        <script type="x-tmpl" id="attachment-tmpl">
            @include('volunteering.partials.add-attachment', ['index' => 'XXX'] )
        </script>

        <script>

            $("body").on("change","[name='attachments[]']",function(){
                $(this).parents("tr").find(".filename").html( $(this)[0].files[0].name);
            });

            let row_number = 0;
            $("#add_row").click(function(e){
                e.preventDefault();
                text = $("#organization-tmpl").html();
                text = text.replace(/XXX/g, row_number + 1);
                $('.organization').last().after( text );
                row_number++;
            });

            let attachment_number = 1;
            $("body").on("click",".add_attachment_row",function(e){
                e.preventDefault();
                text = $("#attachment-tmpl").html();
                text = text.replace(/XXX/g, attachment_number + 1);
                $('.attachment').last().after( text );
                attachment_number++;
            });

            $("body").on("click",".remove",function(e){
                e.preventDefault();
                $(this).parents("tr").remove();
            });

        </script>
    @endpush
@endsection
