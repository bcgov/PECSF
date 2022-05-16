@extends('adminlte::page')
@section('content_header')
    <div class="mt-3">
        <h1>Challenge</h1>
        <p class="h5 mt-3">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad maiores sapiente eos eum corrupti mollitia blanditiis corporis cum totam at repellendus enim delectus recusandae esse, velit illo fugit tenetur non!</p>
    </div>
@endsection
@section('content')
<div class="d-flex justify-content-end">
    <label style="min-width: 130px">
        Campaign Year
        <select class="form-control form-control-sm">
            <option value="2021">2021</option>
            <option value="2020">2020</option>
        </select>
    </label>
</div>
<div class="card">
    <div class="card-body">
        <table class="table table-bordered rounded" id="myTable2">
            <tr class="bg-light">
                <th onclick="sortTable(0)" style="cursor: pointer;">Rank</th>
                <th onclick="sortTable(1)" style="cursor: pointer;">Organization Name</th>
                <th onclick="sortTable(2)" style="cursor: pointer;">Participation Rate</th>
                <th onclick="sortTable(3)" style="cursor: pointer;">Final Participation Rate In Previous Years</th>
                <th onclick="sortTable(4)" style="cursor: pointer;">Change</th>
                <th onclick="sortTable(5)" style="cursor: pointer;">Number of Donors</th>
                <th onclick="sortTable(6)" style="cursor: pointer;">Dollars Donated</th>
            </tr>
            @foreach($charities as $index => $charity)
            <tr>
                <td>{{$index+1}}{{$index == 0 ? 'st' : ($index == 1 ? 'nd' : ($index == 2 ? 'rd' : 'th')) }}</td>
                <td>{{$charity['name']}}</td>
                <td>{{$charity['participation_rate']}}%</td>
                <td>{{$charity['final_participation_rate']}}%</td>
                <td>
                    @if($charity['change'] < 0)
                        <span style="color:red">
                    @else
                        <span style="color:green">
                    @endif
                    {{$charity['change']}}%
                </td>
                <td>{{$charity['total_donors']}}</td>
                <td>${{number_format($charity['total_donation'])}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
@push('js')
<script>
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("myTable2");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc";
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>
@endpush