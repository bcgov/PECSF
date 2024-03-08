<div class="container">

<div class="row">
        <div class="col-4 text-right">
            <p class="font-weight-bold">In support of</p>
          </div>
          <div class="col-8">
            <p>{{ $in_support_of }}</p>
          </div>
    </div>
<div class="row">
      <div class="col-4 text-right">
          <p class="font-weight-bold">Initative</p>
        </div>
        <div class="col-8">
          <p>{{ $special_campaign_name }}</p>
        </div>
</div>

<div class="row">
  <div class="col-4 text-right">
      <p class="font-weight-bold">Campaign Period</p>
    </div>
    <div class="col-8">
      <p>From {{ $pledge->special_campaign->start_date->format('M j, Y') }} to {{ $pledge->special_campaign->end_date->format('M j, Y') }}</p>
    </div>
</div>

<div class="row">
    <div class="col-4 text-right">
        <p class="font-weight-bold">Calendar Year</p>
      </div>
      <div class="col-1">
        <p>{{ $year }}</p>
      </div>
</div>

<div class="row">
      <div class="col-4 text-right">
        <p class="font-weight-bold">Deduct from Pay</p>
      </div>
      <div class="col-8">
        <p>{{ $check_dt->format('Y-m-d') }}</p>
      </div>
    </div>

<div class="row">
    <div class="col-4 text-right">
        <p class="font-weight-bold">One Time Payroll Deduction</p>
      </div>
      <div class="col-1">
        <p>${{ number_format($pledge_amt,2) }}</p>
      </div>
</div>

<div class="row">
    <div class="col-4 text-right">
     <p class="font-weight-bold">Total Amount</p>
      </div>
      <div class="col-1">
        <p>${{ number_format($total_amount,2) }}</p>
      </div>
</div>
