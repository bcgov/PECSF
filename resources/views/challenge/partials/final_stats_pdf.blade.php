
<style>

@page {
  margin: 25px 55px 0;
}

table {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 0.8em;
  border-collapse: collapse;
  width: 100%;
}

thead, tbody {
  border: 3px solid #000 ;
}

tr.logo th {
  background-repeat: no-repeat;
  padding: 0%;
  font-size: 2.0em;
}

tr.logo img {
  width: 100%;
}  

.overlay-text {
  position: absolute;
  top: 0.5%; 
  left: 54%; 
  color: #fff; 
  font-size: 1.2em; 
}


tr.header th {
  font-size: 1.2em;
  padding-top: 4px;
  padding-bottom: 4px;
  text-align: center;
  background-color: #00a7b0;
  color: white;
}

tr.detail:nth-child(even){background-color: rgb(217, 217, 217);}

td:first-child {
  font-weight: bold;
  padding-left: 1em;
}

td:nth-child(2) {
  /* font-weight: 600; */
  text-align: left;
  background-color: rgb(242, 242, 242);
  width: 1%;
}

td:nth-child(3) {
  /* font-weight: 600; */
  text-align: right;
  background-color: rgb(242, 242, 242);
  width: 15%;
}

td:nth-child(4) {
  /* font-weight: 600; */
  text-align: center;
  background-color: rgb(242, 242, 242);
  width: 15%;
}

tr.total td {

  font-weight: bold;
  font-size: 1.2em;
  padding-top: 4px;
  padding-bottom: 4px;
}
 

tr.total td:first-child {
  text-align: right;
  background-color: #00a7b0;
  color: white;
  font-size: 1.0em;
}

tr.total td:nth-child(2) {
  text-align: left;
  background-color: white;
}

tr.total td:nth-child(3) {
  text-align: right;
  background-color: white;
}

tr.total td:nth-child(4) {
  text-align: center;
  background-color: white;
}

tr.date td:first-child {
  background-color: rgb(242, 242, 242);
  font-weight: normal;
  padding-top: 4px;
  padding-bottom: 4px;

}
</style>

<table class="table table-sm">
  <tbody>
    <tr class="logo">
      <th colspan="4">
        <div>
          <img src="img/final_stats_logo.png"/>
          {{-- <img class="pdf-logo-image" style="float:right;width:150px;height:auto;" src="img/final_stats_logo.png"/><br> --}}
          <div class="overlay-text">{{  $campaign_year }}</div>
        </div>
         
      </th>
    </tr>
    <tr class="header">
      <th scope="col">Organization</th>
      <th scope="col"> </th>
      <th scope="col">Total $</th>
      <th scope="col"># Donors</th>
    </tr>
  {{-- </thead>   --}}
  
  {{-- <tbody> --}}
      @foreach( $rows as $row) 
      <tr class="detail">
        <td>{{ $row->organization_name }}</td>
        <td>&nbsp;$</td>
        <td>{{ number_format(round($row->dollars,0),0) }}</td>
        <td>{{ $row->donors }}</td>
      </tr>
      @endforeach
    
      <tr class="total">
        <td>TOTAL FUNDS RAISED AND TOTAL DONORS PROVINCE WIDE &nbsp;</td>
        <td>&nbsp;$</td>
        <td>{{ number_format(round($total_dollars,0) ,0) }}</td>
        <td>{{ $total_donors }}</td>
      </tr>

      <tr class="date">
      @if ($as_of_day) 
          <td>Updated: {{ $as_of_day->copy()->addDay()->format('M d, Y') }}; Stats from {{ $as_of_day->format('M d, Y') }}</td>
      @else
          <td></td>
      @endif
          <td></td>
          <td></td>
          <td></td>
        </tr>
  </tbody>
 
</table>