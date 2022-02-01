<div id="accordion">
    <div class="card">
        <div class="card-header" id="heading">
            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse" aria-expanded="true" aria-controls="collapse">
                <button class="btn btn-link font-weight-bold">
                    {{$currentYear}}
                </button>
                <div class="flex-fill"></div>
                <div class="expander">
                </div>
            </h5>
        </div>
        <div id="collapse" class="collapse show" aria-labelledby="heading" data-parent="#accordion">
            <div class="card-body">
                <table class="table table-bordered rounded">
                    <tr class="text-center bg-light">
                        <th>Organization Name</th>
                        <th>Date Effective</th>
                        <th>Donation Type</th>
                        <th>Frequency</th>
                    </tr>
                    @foreach($pledges as $pledge)
                        @foreach($pledge->charities as $charity)

                        <tr class="text-center">
                            <td class="text-left">{{$charity->charity->charity_name}} </td>
                            <td>{{$pledge->created_at->format('F j, Y')}}</td>
                            <td>{{$pledge->created_at->format('Y')}} Campaign</td>
                            <td>{{$pledge->frequency == 'bi-weekly' ? 'Bi-Weekly' : 'One Time'}}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </table>
                <div class="text-center mt-3">
                    <div class="row">
                        <div class="col-6 px-5 offset-3">
                            <button class="btn btn-block btn-outline-primary">Export Summary</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="heading2">
            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse">
                <button class="btn btn-link font-weight-bold">
                    2021
                </button>
                <div class="flex-fill"></div>
                <div class="expander">
                    
                </div>
            </h5>
        </div>

        <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordion">
            <div class="card-body">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="heading3">
            <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse">
                <button class="btn btn-link font-weight-bold">
                    2020
                </button>
                <div class="flex-fill"></div>
                <div class="expander">
                    
                </div>
            </h5>
        </div>

        <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion">
            <div class="card-body">
            </div>
        </div>
    </div>
</div>