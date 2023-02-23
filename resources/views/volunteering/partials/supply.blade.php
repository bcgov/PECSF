<form id="supply_order_form" method="POST" action="/volunteering/supply_order_form">
<div class="card" style="padding:50px;">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-primary">PECSF Supply Order Form</h1>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label class="text-primary">Supplies</label>
        </div>
    </div>

    <div class="row" style="background:#f7f7f7;border-radius:5px;padding:25px;">
        <label class="col-md-4">
            Calendars
            <input name="calendars" class=" form-control" />
        </label>
        <label class="col-md-4">
            Campaign Posters
            <input name="posters" class=" form-control" />
        </label>
        <label class="col-md-4">
            Stickers
            <input name="stickers" class=" form-control" />
        </label>
    </div>

<br><br>
    <div style="background:#e6edf2;padding:25px;">
        <div class="col-md-12">
            <label class="text-primary">50-50 Gaming Supplies</label>
        </div>
        <div class="col-md-12">
            <label class=" text-md">ONLY the following price points are permitted with the PECSF gaming license:</label>
        </div>
        <div class="col-md-12">
         <ul>
             <li>1 ticket for $2.00</li>
             <li>3 tickets for $5.00</li>
             <li>7 tickets for $10.00</li>
         </ul>
        </div>
        <div class="col-md-12">
            <label class="text-md">Note:</label>
        </div>
        <div class="col-md-12">
            <label class=" text-md">Different coloured tickets for each price point are required by BC Gaming Policy and Enforcement. If you plan to offer all three price points you will need min . 3 rolls or 1 for each price point. <br><br>
            Please ensure you have read and understand gaming requirements before hosting any events. If you have any questions contact the PECSF HQ team (<a href="mailto:pecsf@gov.bc.ca">pecsf@gov.gc.ca</a>) in advance of your event</label>
        </div>
    </div>
<br><br>
    <div class="row" style="background:#f7f7f7;border-radius:5px;padding:25px;">
        <label class="col-md-4">
            1 ticket for $2.00
            <input name="two_rolls" class=" form-control" />
        </label>
        <label class="col-md-4">
            3 tickets for $5.00
            <input name="five_rolls" class=" form-control" />
        </label>
        <label class="col-md-4">
            7 tickets for $10.00
            <input name="ten_rolls" class=" form-control" />
        </label>
    </div>
    <br><br>
    <div class="row">
        <div class="col-md-12">
            <label class="text-primary">Mailing Information</label>
        </div>
    </div>
    <div class="row" style="background:#f7f7f7;border-radius:5px;padding:25px;">
        <label class="col-md-4">
            First Name
            <input name="first_name" class=" form-control" />
        </label>
        <label class="col-md-4">
            Last Name
            <input name="last_name" class=" form-control" />
        </label>
        <label class="col-md-4">
            Organization
            <select class="form-control search_icon" id="business_unit" name="business_unit_id">
                <option value="">Select a business unit</option>
                @foreach($business_units as $bu)
                    @if(!empty($bu->name))
                        <option value="{{$bu->id}}">{{$bu->name}}</option>
                    @endif
                @endforeach
            </select>
            <span class="business_unit_errors errors">
                       @error('business_unit')
                        <span class="invalid-feedback">{{  $message  }}</span>
                    @enderror
                  </span>
        </label>
    </div>
<br><br>
    <div style="background:#e6edf2;padding:25px;">
    Would you like to be recognized as an Office contact PECSF volunteer? *

        As a PECSF volunteer for your organization you will receive a certificate and small gift after the campaign
    </div>


<div class="row p-3">
    <div class="col-md-12 p-3">
        <input type="radio" name="include_name" checked value="1" class="input-control" /> Yes - Include my name as a volunteer
    </div>
    <div class="col-md-12 p-3">
        <input type="radio" name="include_name"  value="0" class="input-control" /> No - Do not Include my name as a volunteer
    </div>
</div>


    <div class="row" style="padding:25px;">
        <h2>Please select a mailing option below:</h2>
    </div>

    <div class="row" style="padding:25px;padding-left:0;padding-right:0;">
    <div class="col-md-8">
        <h3><a target="_blank" style="text-decoration:underline" class="text-primary" href="https://www.canadapost-postescanada.ca/info/mc/personal/postalcode/fpc.jsf">Open Canada Post Find a Postal Code</a></h3>
    </div>
        <div class="col-md-4 justify-content-end" style="text-align:right;">
            <label for="address_type_physical">
                <input id="address_Type_physical" type="radio" checked name="address_type" value="physical" />
                Mail to My Physical Address
            </label>
        </div>
        </div>


    <div class="row" style="position:relative;background:#f7f7f7;border-radius:5px;padding:25px;">
        <label style="left:0px;top:-25px;position:absolute;padding:8px;border-top-left-radius: 5px;border-top-right-radius: 5px;" class="col-md-12 bg-blue">
            Mailing Address
        </label>
        <label class="col-md-4">
            Unit/Suite/Floor
            <input name="unit_suite_floor" class="form-control" />
        </label>
        <label class="col-md-4">
            Physical address (street # and name)
            <input name="physical_address" class="form-control" />
        </label>
        <label class="col-md-4">
            City
            <input name="city" class="form-control" />
        </label>
        <label class="col-md-6">
            Province
            <input name="province" class="form-control" />
        </label>
        <label class="col-md-6">
            Postal Code
            <input name="postal_code" class="form-control" />
        </label>
    </div>
    <div class="row" style="padding:25px;padding-left:0;padding-right:0;">

        <div style="text-align:right;" class="col-md-12 justify-content-end">

            <label for="address_type_po" style="text-align:right;">
                <input id="address_Type_po" type="radio" name="address_type" value="po" />
                Mail to My PO Box Address
            </label>
        </div>
    </div>
    <div class="row" style="position:relative;background:#f7f7f7;border-radius:5px;padding:25px;">
        <label style="left:0px;top:-25px;position:absolute;padding:8px;border-top-left-radius: 5px;border-top-right-radius: 5px;" class="col-md-12 bg-blue">
            PO Box Address
        </label>
        <label class="col-md-12">
            Po Box
            <input name="po" class="form-control" />
        </label>
        <label class="col-md-4">
            City
            <input name="po_city" class="form-control" />
        </label>
        <label class="col-md-4">
            Province
            <input name="po_province" class="form-control" />
        </label>
        <label class="col-md-4">
            Postal Code
            <input name="po_postal_code" class="form-control" />
        </label>
    </div>

    <br><br>
    <div class="row">
        <div class="col-md-12">
            <label class="text-primary">Additional Information</label>
        </div>
    </div>
    <div class="row" style="background:#f7f7f7;border-radius:5px;padding:25px;">
        <label class="col-md-6">
            Date Required
            <input name="date_required" type="date" class=" form-control" />
        </label>
        <label class="col-md-6">
            Comments
            <input name="comments" class=" form-control" />
        </label>
    </div>
    <br><br>
    <input type="submit" value="Submit" class="col-md-1 btn btn-primary"  />

</div>

<div  style="border:black 1px solid;" class="border-dark card">
    Personal information on this form is collected by the BC Public Service Agency for the purpose of sending you PECSF campaign supplies under section 26(c) of the Freedom of Information and Protection of Privacy Act.<br><br>

    By clicking Submit button you hereby consent to the user by the BC Public Service Agency of the address you provided for this purpose.<br><br>

    Questions about the collection of your personal information can be directed to the Campaign Manager Provincial Employees Community Services fund at 250 356-1736 PECSF@gov.bc.ca or PO Box 9564 Stn Prov Govt Victoria BC V8W9C5
</div>



</form>
