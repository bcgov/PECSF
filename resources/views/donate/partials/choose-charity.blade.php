
<div class="form-group org_hook col-md-3">
            <label for="keyword">Search by Keyword</label>
            <input class="form-control" type="text" name="keyword" value="" id="keyword" />
        </div>
        <div class="form-group org_hook col-md-3">
            <label for="category">Search by Category</label>
            <select class="form-control" type="text" name="category" id="category">
                <option value="">Choose a Category</option>

@foreach(\App\Models\Charity::CATEGORY_LIST as $key => $value)
    <option value="{{$key}}">{{$value}}</option>
    @endforeach
    </select>
    </div>
    <div class="form-group org_hook col-md-3">
        <label for="category">Search by Province</label>
        <select class="form-control" type="text" name="province" id="charity_province">
            <option value="">Choose a Province</option>
            @foreach(\App\Models\Charity::PROVINCE_LIST as $key => $value)
                <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>

    <div class="charity-container form-group org_hook  col-md-9">
        <h4 class="blue">Search Results</h4>
        @include("volunteering.partials.organizations")


        <div>
            @if($organizations)
                {{$organizations->links()}}
            @else

            @endif

        </div>
    </div>
    <div class="col-md-3"></div>
        <br>
        <br>

<div class="charity-container form-group org_hook  col-md-9">

        <h4 class="blue">Your Charities</h4>

        <table class="charity-container" id="organizations" style="display:none;width:100%">
            <h5 style="width:100%;text-align:center" class="align-content-center">You have not chosen any charities</h5>
        </table>
</div>
