@if ($type == 'Donate Today')

  <div class="container">
    <div class="row">
        <div class="col-4 text-right">
            <p class="font-weight-bold">In support of</p>
          </div>
          <div class="col-8">
            <p>{{ $donate_today_pledge->name1 }}</p>
          </div>
    </div> 
    <div class="row">
      <div class="col-4 text-right">
          <p class="font-weight-bold"></p>
        </div>
        <div class="col-8">
          <p>{{ $donate_today_pledge->name2 }}</p>
        </div>
    </div> 
    
    <div class="row">
        <div class="col-4 text-right">
          <p class="font-weight-bold">Calendar Year</p>
        </div>
        <div class="col-1">
          <p>{{ $donate_today_pledge->yearcd }}</p>
        </div>
    </div>    

    <div class="row">
      <div class="col-4 text-right">
        <p class="font-weight-bold">Deduct from Pay</p>
      </div>
      <div class="col-8">
        <p>{{ substr(trim($donate_today_pledge->additional_info), -10) }}</p>
      </div>
    </div>    

    <div class="row">
      <div class="col-4 text-right">
      <p class="font-weight-bold">Amount</p> 
        </div>
        <div class="col-1">
          <p>${{ number_format($donate_today_pledge->amount,2) }}</p>
        </div>
    </div>


  </div>

@else

    <div class="container">
    <div class="row">
        <div class="col-4 text-right">
            <p class="font-weight-bold">Year</p>
          </div>
          <div class="col-1">
            <p>{{ $year }}</p>
          </div>
    </div>    
    <div class="row">
        <div class="col-4 text-right">
            @if ($frequency == 'One-Time')
              <p class="font-weight-bold">One Time Payroll Deduction</p> 
            @else
              <p class="font-weight-bold">Bi-weekly payroll Deduction</p> 
            @endif
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
    @if ($old_pledges->first()->campaign_type == 'Event')
      <div class="row">
        <div class="col-4 text-right">
        <p class="font-weight-bold">Deposit Date</p> 
          </div>
          <div class="col-6">
            <p>{{ $old_pledges->first()->event_deposit_date }}</p>
          </div>
      </div>    

      <div class="row">
        <div class="col-4 text-right">
        <p class="font-weight-bold">Event Type</p> 
          </div>
          <div class="col-6">
            <p>{{ $old_pledges->first()->event_type }}</p>
          </div>
      </div>    
      <div class="row">
        <div class="col-4 text-right">
        <p class="font-weight-bold">Event Sub-type</p> 
          </div>
          <div class="col-6">
            <p>{{ $old_pledges->first()->event_sub_type }}</p>
          </div>
      </div>    
    @endif

    @if ($pool_name)
    <div class="row">
        <div class="col-4 text-right">
        <p class="font-weight-bold">Fund Supported Pool</p> 
          </div>
          <div class="col-6">
            <p>{{ $pool_name }}</p>
          </div>
    </div>    
    @endif


  <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col"></th>
          <th scope="col">Benefitting Charity</th>
          <th scope="col">Percent</th>
          <th scope="col">Amount</th>
        </tr>
      </thead>
      <tbody>
          @foreach ($old_pledges as $pledge)           
              <tr>
                  <td scope="row">{{ $loop->index +1 }}</td>
                  <td>
                      <p>{{ $pledge->name1 ?? 'N/A' }}</p>
                      <p>{{ $pledge->name2 ?? 'N/A' }}</p>
                  </td>
                  <td class="text-center">{{ number_format($pledge->percent,2) }}%</td>
                  <td class="text-center">${{ number_format(($total_amount * $pledge->percent / 100),2) }}</td>
              </tr>
          @endforeach    
      </tbody>
    </table>

  </div>

@endif