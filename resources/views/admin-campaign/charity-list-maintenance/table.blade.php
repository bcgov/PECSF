@foreach ( $charities as $index => $charity)
    <tr>
        <td>{{ $charity->charity_name }}</td>
        <td>{{ $charity->registration_number }}</td>
        <td><button id="charity-{{ $charity->id }}">View / Edit</button></td>
    </tr>
    @endforeach
