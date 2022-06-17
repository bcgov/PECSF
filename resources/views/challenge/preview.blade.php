<table class="table">


@foreach ($rows as $index => $charity)
    @if($index == 0)
        <thead>
        <tr>
        @foreach( $charity as $key => $title)
            <th>{{$title}}</th>
        @endforeach
        </tr>
        </thead>
        @continue
    @endif

    <tr>
        @foreach( $charity as $key => $title)
            <td>{{$title}}</td>
        @endforeach
    </tr>
    @endforeach

    @if($request->sort == "department")
        <tr>
            <td>Total</td>
            <td></td>
            <td></td>

            <td>{{$dollarTotal}}</td>
        </tr>
    @else
        <tr>
            <td>Total</td>
            <td>{{$donorTotal}}</td>
            <td>{{$dollarTotal}}</td>
        </tr>
    @endif


</table>
