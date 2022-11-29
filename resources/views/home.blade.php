@extends('adminlte::page')

@section('content')

<style>
    p.text-primary{
        font-size:12px;
        color:#1a5a96;
        font-weight:bold;
    }

    .card:hover span{
        text-decoration-color: white;
        color:white;
        text-decoration: underline;
    }

    .card:hover p{
        text-decoration-color: white;
        color:white;
        text-decoration: underline;
    }
</style>
<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-xl-12 offset-md-1">
            <h1 class="text-center">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-center h4"><b>Choose from the options below:</b></p>

            <div class="row p-3">
                <div class="card_hook col-md-3 p-2">
                    <div class="card" style="height:auto" >
                        <a class="card-body text-center"  href="{{route('volunteering.index')}}">
                            <i class="x nav-icon fas fa-hands-helping fa-2x "></i>
                          <p class="text-primary "> <span style="font-size:20px;">Volunteering</span></p>
                          <p class="mt-1"> Looking to do more than just donate? Volunteer to help run a campaign or host an event.</p>
                        </a>
                    </div>
                </div>
                <div class="card_hook col-md-5 p-2">
                    <div class="card" style="height:333px" >
                        <a href="{{route('donations.list')}}" class="card-body text-center">
                            <img src="/svgs/give.svg" style="color:white;" alt="Connect" height="62">
                            <p class="text-primary "> <span style="font-size:22px;" >Donations </span></p>
                            <p>Support the charities of your choice with payroll deduction in any amount.</p>
                        </a>
                    </div>
                </div>
                <div class="card_hook col-md-3 p-2">
                    <div class="card" style="height:auto;" >
                        <a href="{{route('contact')}}" class="card-body text-center">
                            <i class="x nav-icon fas fa-question-circle fa-2x "></i>
                            <p class="text-primary ">  <span style="font-size:22px;">Contact </span></p>
                            <p class="mt-1">Got questions? We are here to help!</p>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<div class="mx-n3 mt-5 bg-primary">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-5 offset-md-1 pt-5">
        <br><br>
        <br><br>
        <h1 class="mt-5 p1-5" style="font-size: 4em;">
          Generosity in Action
        </h1>
      </div>
      <div class="col-12 col-md-6">
        <div class="py-5">
          <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img class="d-block w-100" src="{{asset('img/home/01.jpeg')}}" alt="First slide">
              </div>
              <div class="carousel-item">
                <img class="d-block w-100" src="{{asset('img/home/02.jpg')}}" alt="Second slide">
              </div>
              <div class="carousel-item">
                <img class="d-block w-100" src="{{asset('img/home/04.jpg')}}" alt="Third slide">
              </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Back</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-xl-8 offset-xl-2">
            <h1 class="text-center">Check out our leaderboard</h1>

            <div class="row p-3">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered rounded" id="myTable2">
                            <tr class="bg-light">
                                <th onclick="sortTable(0)" style="cursor: pointer;">Rank</th>
                                <th onclick="sortTable(1)" style="cursor: pointer;">Organization Name</th>
                                <th onclick="sortTable(2)" style="cursor: pointer;">Participation Rate (%)</th>
                                <th onclick="sortTable(2)" style="cursor: pointer;">Previous Participation Rate (%)</th>
                                <th onclick="sortTable(5)" style="cursor: pointer;">Donors</th>
                                <th onclick="sortTable(6)" style="cursor: pointer;">Dollar Donated</th>
                            </tr>
                            @foreach($charities as $index => $charity)
                            <tr>
                                <td>{{$index+1}}{{$index == 0 ? 'st' : ($index == 1 ? 'nd' : ($index == 2 ? 'rd' : 'th')) }}</td>
                                <td>{{$charity['name']}}</td>
                                <td>{{round(($charity['participation_rate'] * 100),2)}}%</td>
                                <td>{{round(($charity['previous_participation_rate'] * 100),2)}}%</td>

                                <td>{{$charity['donors']}}</td>
                                <td>${{$charity['dollars']}}</td>
                            </tr>
                            @endforeach
                        </table>
                        <p></p>
                        <div class="text-center">
                            <a class="btn btn-primary" href="/challenge" role="button">Go to challenge page</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection

@push('js')
<script>
    var mouseOutTimer = false;

    function reset(){
        $(".card_hook .card").css("background","white");
        $(".card_hook .card").find("span").css("color","#1a5a96");
        $(".card_hook .card").find("i").css("color","#1a5a96");
        $(".card_hook .card").find("p").css("color","#000");
        $(".card_hook .card").find("p.text-primary").css("color","#1a5a96");
        $(".card_hook .card").find("img").css("filter","none");
    }

    $(".card_hook").mouseout(function(){
        clearTimeout(mouseOutTimer);
        mouseOutTimer = setTimeout(function(){
            reset();
        },200);
    });

    $(".card_hook").mouseover(function(){
        clearTimeout(mouseOutTimer);
    });

    $(".card_hook").hover(function(){
        reset();
        $(this).find(".card").css("background","#1a5a96");
        $(this).find(".card span").css("color","#fff");
        $(this).find(".card i").css("color","#fff");
        $(this).find(".card img").css("filter","invert(100%) sepia(100%) saturate(0%) hue-rotate(248deg) brightness(106%) contrast(106%)");
        $(this).find(".card p").css("color","#fff");
    });

    $(".card_hook").mouseout(function(){
      /*  $(".card_hook").removeClass("col-md-3");
        $(".card_hook").removeClass("col-md-5");



        $(".card_hook")[1].addClass("col-md-5");
        $(".card_hook")[0].addClass("col-md-3");
        $(".card_hook")[2].addClass("col-md-3");

*/
    });

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
