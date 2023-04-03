<div id="{{$keyCase}}Section" class="amountDistributionSection">
    @if (($viewMode ?? '') !== 'pdf')
    <p class="mt-4"><b>Your {{($key_)}} charitable donations will be disbursed as follows:</b></p>
    <div class="d-flex align-items-center justify-content-between mb-3">
       <!-- <div class="form-check form-switch p-0">
            <label class="form-check-label" for="distributeByDollarAmount{{ucfirst($keyCase)}}">
                <input class="form-check-input" type="checkbox" id="distributeByDollarAmount{{ucfirst($keyCase)}}" name="distributionByPercent{{ucfirst($keyCase)}}" value="1" checked>
                <i></i><span class="percent-amount-text">Distribute by Dollar Amount</span>
            </label>
        </div> -->

        <div class="btn-group btn-group-toggle mt-3 frequency frequency{{$keyCase}}" role="group"  aria-label="Select frequency" data-toggle="buttons">
            <label class="btn btn-outline-primary btn-lg active" for="distributeByPercentage">
                <input type="radio"  checked class="btn-check"  autocomplete="off"  id="distributeByPercentage" name="distributionByPercent{{ucfirst($keyCase)}}" value="1" >
                Percentage
            </label>
            <label class="btn btn-outline-primary btn-lg" for="distributeByDollar">
                <input type="radio"  class="btn-check"  autocomplete="off"  id="distributeByDollar" name="distributionByPercent{{ucfirst($keyCase)}}" value="1" >
                Dollar amount
            </label>
        </div>

    </div>
    @endif

    <div class="card mt-3">
        <div class="card-body">
            @if (($viewMode ?? '') == 'pdf')
                <h2>{{ $key == 'one-time' ? 'One Time' : 'Bi-Weekly' }} donation disbursement</h2>
                <hr>
            @endif
            <table class="table table-sm">
                @foreach ($charities as $charity)
                <tr>
                    <td class="p-2">
                        {{ $charity['text'] }} <br>
                        <small>
                            {{ $charity['additional']}}
                        </small>
                    </td>
                    @if (($viewMode ?? '') !== 'pdf')
                    <td style="width:130px" class="by-percent ">
                        <div class="input-group input-group-sm mb-3" style="flex-direction:column">
                            <input type="hidden" class="form-control form-control-sm percent-input float-right text-right" name="{{$keyCase}}Percent[{{ $charity['id'] }}]" value='{{$charity["$key_-percentage-distribution"]}}' disabled>
                            <label class="float-right text-right">{{ number_format($charity["$key_-percentage-distribution"],2) }}%</label>
                        </div>
                    </td>
                    @endif
                    <td style="width:130px" class="by-amount d-none">
                        <div class="input-group input-group-sm mb-3" style="flex-direction:column">
                            <input type="hidden" class="form-control form-control-sm amount-input float-right text-right"  name="{{$keyCase}}Amount[{{ $charity['id'] }}]" value='{{$charity["$key_-amount-distribution"]}}' disabled>
                            <label class="float-right text-right"> ${{ number_format( $charity["$key_-amount-distribution"] * $multiplier,2) }}</label>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td></td>
                    @if (($viewMode ?? '') !== 'pdf')
                    <td class="by-percent">
                        <div class="input-group input-group-sm mb-3 text-right" style="flex-direction:column;">
                            <input type="hidden" class="form-control form-control-sm total-percent" readonly>
                            <label class="total-percent-text float-right" style="width:250px;"><b>Total:</b> 100%</label>
                        </div>
                    </td>
                    @endif
                    <td class="by-amount d-none">
                        <div class="input-group input-group-sm mb-3 text-right" style="flex-direction:column;">
                            <input type="hidden" class="form-control form-control-sm total-amount" value="{{ ${'annual'.ucfirst($keyCase).'Amount'} }}" readonly>
                            <label class="total-amount-text float-right" style="width:250px;" ><b>Total:</b> ${{ number_format( round(${'annual'.ucfirst($keyCase).'Amount'},2) ,2) }}</label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
