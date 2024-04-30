@extends('adminlte::page')
@section('content_header')
    <div class="row pl-3 pt-4">
        <h1>Volunteering in the BC Public Service</h1>
    </div>

    <div class="mx-1 pt-3">
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('volunteering.index') }}'">
            Back
        </button>
    </div>

@endsection
@section('content')

    <div class="content p-5">
   
    <div class="row p-1">
        <h2>Welcome to returning volunteers, new volunteers and those of you thinking of volunteering! </h2>
        <div>Welcome to the volunteer section of the PECSF app.  This section will have increased functionality in future releases of the app.  As always, we welcome your feedback, if you have any suggestions or comments, please feel free to share them with us in our <a href="https://forms.office.com/pages/responsepage.aspx?id=AFLbbw09ikqwNtNoXjWa3Ai9wfjgvSFOiS5TXhN1jFFUN1gxTjE4VkkzNUpMTFFRV1ZGVTVORTdCNCQlQCN0PWcu" target="_blank">volunteer feedback survey.</a>
        </div>
        <table class="table mt-4" style="width:100%;text-align:center;">
            <tr>
                <td style="width:200px;text-align:center;">
                    <img style="width:200px;" src="/img/volunteering.png" alt="Caring for Communties since 1965" />

                </td>
                <td style="text-align:left;font-size:26px;font-weight:bold;vertical-align:middle;">
                    " Volunteers do not necessarily have the time; they just have the heart. " ~ Elizabeth Andrew
                </td>
            </tr>
        </table>

    </div>
    <h2>Volunteering</h2>
    <div class="row p-1">
<div>      We thank you for volunteering with the PECSF program. If you want to help by volunteering with your ministry/organizations team, contact your <a href="https://bcgov.sharepoint.com/teams/056772/SitePages/Resources.aspx#organization-ministry-contact-information-ministries" target="_blank">&nbsp;PECSF Lead Coordinator.</a>
    &nbsp;Potential ways to help with your office’s campaign:</div>


        <ul class="pl-5">
            <li>	Canvasser
            </li>
            <li>	Event coordinator
            </li>
            <li>	Communications
            </li>
            <li>	50/50 ticket coordinator
            </li>
            <li>	And so much more…
            </li>
        </ul>

    </div>
    <br><h2>Training</h2>
    <div class="row p-1">
        Registration for PECSF courses is available in the Learning Centre’s PSA Learning System. Once you are in the system search “PECSF” and register for one of the following three courses.  Registration opens in June for courses in August and September.


        <ul class="pl-5">
            <li>	PECSF 101 – Did you know? Canvasser Training
            </li>
            <li>	PECSF Gaming and Events – Know Your Limit!
            </li>
            <li>	PECSF Lead Coordinator
            </li>
        </ul>
    </div>
    <br><h2>Resources</h2>
    <div class="row p-1">
        <div>Visit the &nbsp;<a href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/volunteer/resources" target="_blank">volunteer resource section </a>on the PECSF website for all your campaign resources including campaign start-up and promotional material, document templates and logos as well as fundraising and gaming event guidelines and so much more.</div>
    </div>
    <br><h2>Blogs</h2>
    <div class="row p-1">
       <div>We want to hear from you!  Contact us today at <a href="mailto:PECSF@gov.bc.ca" target="_blank">&nbsp;PECSF@gov.bc.ca</a> to share a story about your favourite charity or why you chose to volunteer with PECSF.  You will find inspiring blogs from charities and volunteers on our <a target="_blank" href="https://bcgov.sharepoint.com/teams/056772/SitePages/News-and-Blogs.aspx">&nbsp;PECSF Community Connect SharePoint.</a></div>
    </div>
    <br><h2>Contact</h2>

    <div class="row p-2">
        <strong>If you have any questions or are interested in volunteering for the 2023 Campaign, please email Kristina Allsopp at <a href="mailto:PECSF@gov.bc.ca" target="_blank">PECSF@gov.bc.ca.</a></strong>
    </div>
<!--
    <div class="d-flex mt-3">
        <h1>Dashboard</h1>
        <div class="flex-fill"></div>
    </div>
    <div class="row">
        <div class="col-md-12 justify-content-center pt-3 mb-5">
            <div class="card justify-content-center border-warning text-center" style="background:#D9EAF7;border-radius: 1em;">
                <div class=" justify-content-center card-body" style="color:#1a5a96;">

                    @if(empty($settings->volunteer_language))
                        <h5 class="card-title"></h5>
                        <p class="card-text text-center">It's time for you to renew your volunteer registration</p>
                        <p class="card-text text-center">
                            Click below to make any necessary updates to your information
                        </p>
                    @else
                        @php
                            echo $settings->volunteer_language ;

                        @endphp
                    @endif
                    <p>
                        <button class="btn btn-primary" onclick="$('#edit-event-modal').modal('show');">Renew volunteer Registration</button>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    -->
    </div>
    
@endsection


@push('css')

<style>
        /* Should be override in app.scss */
    .content-wrapper  a {
        text-decoration: underline !important; 
    }

</style>
    
@endpush

@push('js')
<script>

</script>
@endpush

