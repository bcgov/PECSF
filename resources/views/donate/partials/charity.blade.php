<div class="container">
    <div class="row py-2">
        <div class="col font-weight-bold"><h5>{{ $charity->charity_name }}</h5></div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Business/Registration number:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->registration_number }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Charity Status:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->charity_status }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Effective date of status:</div>
        <div class="col col-sm-8 font-weight-bold">
            {{ Carbon\Carbon::createFromDate($charity->effective_date_of_status)->toFormattedDateString() }}
        </div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Sanction:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->sanction }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Designation:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->designation_name }}</div>
    </div>
    {{--  
    <div class="row">
        <div class="col col-sm-4">Charity type:</div>
        <div class="col col-sm-8 font-weight-bold"></div>
    </div>
    --}}
    <div class="row">
        <div class="col col-sm-4">Category:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->category_name }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Address:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->address }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">City:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->city }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Province, territory:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->province }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Country:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->country }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Postal code/Zip code:</div>
        <div class="col col-sm-8 font-weight-bold">{{ $charity->postal_code }}</div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Website:</div>
        <div class="col col-sm-8"><span class="small"><a href="http://{{ $charity->url }}" target="_blank">{{ $charity->url }}</a></span>
        </div>
    </div>
    <div class="row">
        <div class="col col-sm-4">Charitable Programs:</div>
        <div class="col col-sm-8"><span class="small">{{ $charity->ongoing_program }}</span>
        </div>
    </div>

</div>
