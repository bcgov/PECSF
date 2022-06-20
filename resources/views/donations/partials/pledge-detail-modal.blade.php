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
@if ($pledge->type == 'P')
<div class="row">
    <div class="col-4 text-right">
     <p class="font-weight-bold">Fund Supported Pool</p> 
      </div>
      <div class="col-6">
        <p>{{ $pledge->fund_supported_pool->region->name }}</p>
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
        @if ($pledge->type == 'P')
            @foreach($pledge->fund_supported_pool->charities as $pool_charity)
                <tr>
                    <td scope="row">{{ $loop->index +1 }}</td>
                    <td>
                        <p>{{ $pool_charity->charity->charity_name }}</p>
                        <p>{{ $pool_charity->name  }}</p>
                    </td>
                    <td class="text-center">{{ number_format($pool_charity->percentage,2) }}%</td>
                    <td class="text-center">${{ number_format($total_amount * $pool_charity->percentage / 100, 2) }}</td>
                </tr>
            @endforeach    
        @else
            @foreach($pledge->charities->where('frequency', strtolower($frequency)) as $pledge_charity)
                <tr>
                    <td scope="row">{{ $loop->index +1 }}</td>
                    <td>
                        <p>{{ $pledge_charity->charity->charity_name }}</p>
                        <p>{{ $pledge_charity->additional  }}</p>
                    </td>
                    <td class="text-center">{{ number_format($pledge_charity->percentage,2) }}%</td>
                    <td class="text-center">${{ number_format($total_amount * $pledge_charity->percentage / 100, 2) }}</td>
                </tr>
            @endforeach   
        @endif


    </tbody>
  </table>

