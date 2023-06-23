<div id="{{$keyCase}}Section" class="amountDistributionSection">
    <p class="h4 text-primary">{{ucfirst($key_)}} donation</p>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="form-check form-switch p-0">
            <!--<label class="form-check-label" for="distributeByDollarAmount{{ucfirst($keyCase)}}">
                <input class="form-check-input" type="checkbox" id="distributeByDollarAmount{{ucfirst($keyCase)}}" name="distributionByPercent{{ucfirst($keyCase)}}" value="1" checked>
                <i></i><span class="percent-amount-text">Distribute by Dollar Amount</span>
            </label>-->

            <div class="btn-group btn-group-toggle mt-3 frequency frequency{{$keyCase}}" role="group"  aria-label="Select frequency" data-toggle="buttons">
                <label class="btn btn-outline-primary btn-lg active" for="distributeByPercentage{{ucfirst($keyCase)}}">
                    <input type="radio" checked class="btn-check"  autocomplete="off"  id="distributeByPercentage{{ucfirst($keyCase)}}" name="distributionByPercent{{ucfirst($keyCase)}}" value="0" >
                    Percentage
                </label>
                <label class="btn btn-outline-primary btn-lg" for="distributeByDollar{{ucfirst($keyCase)}}">
                    <input type="radio"  class="btn-check"  autocomplete="off"  id="distributeByDollar{{ucfirst($keyCase)}}" name="distributionByPercent{{ucfirst($keyCase)}}" value="1" >
                    Dollar amount
                </label>
            </div>
        </div>
        <button type="button" class="distribute-evenly btn btn-link">Distribute evenly</button>
    </div>
    <table class="table table-sm">
        @foreach ($selected_charities as $charity)
        <tr>
            <td class="p-2">
                {{ $charity['text'] }} <br>
                <small>
                    {{ $charity['additional']}}
                </small>
            </td>
            <td style="width:140px" class="by-percent">
                <div class="input-group input-group-sm mb-3">
                    <input type="number" class="form-control form-control-sm percent-input" name="{{$keyCase}}Percent[{{ $charity['id'] }}]" placeholder="" value='{{ number_format($charity["$key_-percentage-distribution"],2) }}'>
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </td>
            <td style="width:140px" class="by-amount d-none">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="number" class="form-control form-control-sm amount-input" name="{{$keyCase}}Amount[{{ $charity['id'] }}]" placeholder="" value='{{ number_format($charity["$key_-amount-distribution"],2) }}'>
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
                    <input type="text" class="form-control form-control-sm total-percent" placeholder="" 
                        value='{{ (ucfirst($key_) == 'Bi-weekly') ? number_format($calculatedTotalPercentBiWeekly,2) : number_format($calculatedTotalPercentOneTime,2) }}' disabled>
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
                    {{-- <input type="number" class="form-control form-control-sm total-amount" data-expected-total='{{session("amount-step")["$key_-amount"]}}' placeholder="" disabled> --}}
                    <input type="number" class="form-control form-control-sm total-amount" 
                      data-expected-total='{{ (ucfirst($key_) == 'Bi-weekly') ? $last_bi_weekly_amount : $last_one_time_amount }}' 
                      value='{{ (ucfirst($key_) == 'Bi-weekly') ? number_format($calculatedTotalAmountBiWeekly,2) : number_format($calculatedTotalAmountOneTime,2) }}' disabled>
                </div>
            </td>
            {{-- <td></td> --}}
        </tr>
    </table>
</div>
