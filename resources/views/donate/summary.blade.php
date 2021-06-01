@extends('donate.layout.main')

@section ("content")

<div class="container">
    <div class="row">
        <div class="col-12 col-sm-7">
<h2 class="mt-5">4. Summary</h2>
<p class="mt-3">Please review your donation plan and press <b>Pledge</b> when ready!</p>
<div class="card bg-light p-3">
<p class="card-title"><b>Deductions</b></p>

<div class="card">
<div class="card-body">


<span><b>Your bi-weekly payroll deductions:</b></span>
<span class="float-right mb-2">${{ $weekly }}</span><br>
<h6>AND / OR</h6>
<span><b>Your One-Time payroll deductions:</b></span>
<span class="float-right">${{ $onetime }}</span>
<hr>
<p class="text-right"><b>Total :</b> {{ $annual_amount }}</p>
</div>
</div>
<p class="card-title mt-4"><b>Your bi-weekly charitable donations will be disbursed as follows:</b></p>
<form action="{{route('donate.save.distribution')}}" method="POST">

<div class="d-flex align-items-center justify-content-between mb-3 ">
    <div class="form-check form-switch p-0">
        <label class="form-check-label" for="distributeByDollarAmount">
            <input class="form-check-input" type="checkbox" id="distributeByDollarAmount" name="distributionByAmount" value="true" checked>
            <i></i>Distribute by Percentage
        </label>
    </div>
   
</div>

<div class="card mt-3">
<div class="card-body">
    @csrf
    <table class="table table-sm">
        @foreach ($charities as $charity)
        <tr>
            <td class="p-2">{{ $charity['text'] }}</td>
            <td style="width:110px" class="by-percent ">
                <div class="input-group input-group-sm mb-3" style="flex-direction:column">
                    <input type="hidden" class="form-control form-control-sm percent-input float-right text-right" name="percent[{{ $charity['id'] }}]" value="{{$charity['percentage-distribution']}}" disabled>
                    <label class="float-right text-right">{{$charity['percentage-distribution']}}%</label>
                </div>
            </td>
            <td style="width:110px" class="by-amount d-none">
                <div class="input-group input-group-sm mb-3" style="flex-direction:column">
               
                    <input type="hidden" class="form-control form-control-sm amount-input float-right text-right" name="amount[{{ $charity['id'] }}]" value="{{$charity['amount-distribution']}}" disabled>
                 <label class="float-right text-right"> ${{ $charity['amount-distribution'] * 26 }}</label>
            
                </div>
            </td>
         
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td class="by-percent">
                <div class="input-group input-group-sm mb-3" style="flex-direction:column;width:150px">
                    <input type="hidden" class="form-control form-control-sm total-percent" readonly>
                    <label class="total-percent-text float-right" style="width:250px;">%</label>
                </div>
            </td>
            <td class="by-amount d-none">
                <div class="input-group input-group-sm mb-3 text-right" style="flex-direction:column">
                    <input type="hidden" class="form-control form-control-sm total-amount" value="{{ $annual_amount }}" readonly>
                    <label class="total-amount-text float-right" style="width:250px;" >${{ $annual_amount }}</label>
                </div>
            </td>
        </tr>
    </table>
    </div>
</div>
</div>
<div class="row">
<p class="py-4">
Please note that <b>this is not a tax receipt</b>.  
Payroll Deductions begin with the first paycheque in January and will appear on your T4 issued from payroll from that calendar year. PECSF issues cheques twice a year. 
In August for payroll deductions from January -June, and in March for payroll deductions from July -December.
</p>
</div>
<div class="">
    <a class="btn btn-lg btn-outline-primary" href="{{route('donate.distribution')}}">Previous</a>
    <button class="btn btn-lg btn-primary" type="submit">Pledge</button>
</div>
</form>

  
        </div>
        <div class="col-12 col-sm-5">
            <img src="{{ asset('img/donor.png') }}" alt="Donor" class="py-5 img-fluid">
            <p>
            Making changes to your pledge outside of Campaign time? Please contact <a href="mailto:PECSF@gov.bc.ca" class="text-primary">PECSF@gov.bc.ca</a> directly or submit an <a href="https://www2.gov.bc.ca/gov/content/careers-myhr" class="text-primary" target="_blank">AskMyHR</a> service request to make any changes on your existing pledge outside the annual campaign/open enrollment period (September-December). 
            </p>
            <p><b>Questions?</b> <a href="http://www.gov.bc.ca/pecsf" class="text-primary" target="_blank">www.gov.bc.ca/pecsf</a>         <b>Email:</b> <a href="mailto:PECSF@gov.bc.ca" class="text-primary">PECSF@gov.bc.ca</a></p>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/custom-switch.css') }}">
@endpush
@push('js')
    <script>
        $(document).on('change', '#distributeByDollarAmount', function () {
            if (!$(this).prop("checked")) {
                $(".by-amount").removeClass("d-none");
                $(".by-percent").addClass("d-none");
            } else {
                $(".by-percent").removeClass("d-none");
                $(".by-amount").addClass("d-none");
                
            }
        });
        $(document).on('change', '.percent-input', function () {
            let total = 0;
            $(".percent-input").each( function () {
                total += Number($(this).val());
            });
            $(".total-percent").val(total);
            $(".total-percent-text").text('Total Amount: '+total + '%');
        });


        $(document).on('change', '.amount-input', function () {
            let total = 0;
            $(".amount-input").each( function () {
                total += Number($(this).val()) * 26;
            });
  //          $(".total-amount").val(total);
             $(".total-amount-text").text('Total Amount: $'+total);
        });

        $(".percent-input").change();
        $(".amount-input").change();

    </script>
@endpush