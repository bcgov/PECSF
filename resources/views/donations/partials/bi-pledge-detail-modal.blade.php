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
        <p class="font-weight-bold">Bi-weekly payroll deduction</p> 
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
                    <p>{{ $pledge->charity ? $pledge->charity->charity_name : 'N/A' }}</p>
                    <p>{{ $pledge->charity ? $pledge->charity->registration_number : 'N/A' }}</p>
                </td>
                <td class="text-center">{{ number_format($pledge->percent,2) }}%</td>
                <td class="text-center">${{ number_format($pledge->amount * $number_of_periods,2) }}</td>
            </tr>
        @endforeach    
    </tbody>
  </table>

</div>