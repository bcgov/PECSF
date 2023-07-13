<div id="{{$keyCase}}Section" class="amountDistributionSection">
    <p class="mt-4"><h1>Disbursement</h1></p>
    <div class="card mt-3">
        <div class="card-body">
            <h1>Bi-Weekly donation disbursement</h1>
            <hr>
            <table class="table table-sm">
                {{var_dump($charities)}}
                @foreach ($charities as $charity)
                    <tr>
                        <td class="p-2">
                            {{var_dump($charity)}}
                            {{ $charity['text'] }} <br>
                            <small>
                                {{ $charity['additional']}}
                            </small>
                        </td>
                        @if (($viewMode ?? '') !== 'pdf')
                            <td style="width:110px" class="by-percent ">
                                <div class="input-group input-group-sm mb-3" style="flex-direction:column">
                                    <input type="hidden" class="form-control form-control-sm percent-input float-right text-right" name="{{$keyCase}}Percent[{{ $charity['id'] }}]" value='{{$charity["$key_-percentage-distribution"]}}' disabled>
                                    <label class="float-right text-right">{{ number_format($charity["$key_-percentage-distribution"],2) }}%</label>
                                </div>
                            </td>
                        @endif
                        <td style="width:110px" class="by-amount d-none">
                            <div class="input-group input-group-sm mb-3" style="flex-direction:column">
                                <input type="hidden" class="form-control form-control-sm amount-input float-right text-right"  name="{{$keyCase}}Amount[{{ $charity['id'] }}]" value='{{$charity["$key_-amount-distribution"]}}' disabled>
                                <label class="float-right text-right"> ${{ number_format($charity["$key_-amount-distribution"] * $multiplier,2) }}</label>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    @if (($viewMode ?? '') !== 'pdf')
                        <td class="by-percent">
                            <div class="input-group input-group-sm mb-3" style="flex-direction:column;width:150px">
                                <input type="hidden" class="form-control form-control-sm total-percent" readonly>
                                <label class="total-percent-text float-right" style="width:250px;">%</label>
                            </div>
                        </td>
                    @endif
                    <td class="by-amount d-none">
                        <div class="input-group input-group-sm mb-3 text-right" style="flex-direction:column">
                            <input type="hidden" class="form-control form-control-sm total-amount" value="{{ ${'annual'.ucfirst($keyCase).'Amount'} }}" readonly>
                            <label class="total-amount-text float-right" style="width:250px;" ><b>Total:</b> ${{ number_format(${'annual'.ucfirst($keyCase).'Amount'},2) }}</label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
