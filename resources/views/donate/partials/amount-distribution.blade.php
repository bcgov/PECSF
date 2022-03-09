<div id="{{$keyCase}}Section" class="amountDistributionSection">
    <p class="h4 text-primary">{{ucfirst($key_)}} donation</p>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="form-check form-switch p-0">
            <label class="form-check-label" for="distributeByDollarAmount{{ucfirst($keyCase)}}">
                <input class="form-check-input" type="checkbox" id="distributeByDollarAmount{{ucfirst($keyCase)}}" name="distributionByPercent{{ucfirst($keyCase)}}" value="1" checked>
                <i></i><span class="percent-amount-text">Distribute by Dollar Amount</span>
            </label>
        </div>
        <button class="btn btn-link">Distribute evenly</button>
    </div>
    <table class="table table-sm">
        @foreach ($charities as $charity)
        <tr>
            <td class="p-2">
                {{ $charity['text'] }} <br>
                <small>
                    {{ $charity['additional']}}
                </small>
            </td>
            <td style="width:110px" class="by-percent ">
                <div class="input-group input-group-sm mb-3">
                    <input type="number" step="0.01" class="form-control form-control-sm percent-input" name="{{$keyCase}}Percent[{{ $charity['id'] }}]" placeholder="" value='{{$charity["$key_-percentage-distribution"]}}'>
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </td>
            <td style="width:110px" class="by-amount d-none">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01" class="form-control form-control-sm amount-input" name="{{$keyCase}}Amount[{{ $charity['id'] }}]" placeholder="" value='{{$charity["$key_-amount-distribution"]}}'>
                </div>
            </td>
            {{-- <td>
                <div class="d-flex flex-row">
                    <button class="btn border btn-sm btn-light me-1">-</button>
                    <button class="btn border btn-sm btn-light ms-1">+</button>
                </div>
            </td> --}}
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td class="by-percent">
                <div class="input-group input-group-sm mb-3">
                    <input type="text" class="form-control form-control-sm total-percent" placeholder="" disabled>
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </td>
            <td class="by-amount d-none ">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="number" class="form-control form-control-sm total-amount" data-expected-total='{{session("amount-step")["$key_-amount"]}}' placeholder="" disabled>
                </div>
            </td>
            <td></td>
        </tr>
    </table>
</div>