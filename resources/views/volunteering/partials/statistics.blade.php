<div class="row">
    <div class="col-12 col-md-4">
        <div class="card">
            <div class="card-body text-center my-3">
                <span class="h1 text-primary">10</span>
                <p>Years of BC Government service.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card ">
            <div class="card-body text-center my-3 pb-18">
                <span class="h1 text-primary">{{$user->volunteer[0]->no_of_years ?? 'N/A'}}</span>
                <p>Years as a volunteer</p>
                <a href="/profile"><button onclick="" class="btn btn-primary edit justify-content-center">Update / Register Volunteer Role</button></a>

            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card">
            <div class="card-body text-center my-3">
                <span class="h1 text-primary">${{$totalPledgedDataTillNow}}</span>
                <p>Dollars Donated to Date</p>
            </div>
        </div>
    </div>
</div>
