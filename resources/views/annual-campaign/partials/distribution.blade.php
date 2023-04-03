{{-- <input type="hidden" name="last_distributions" value="{{ json_encode($selected_charities) }}"> --}}
<input type="hidden" name="last_selected_charities" value="{{ json_encode( isset($last_selected_charities) ? $last_selected_charities : [] ) }}">


@foreach(['one-time', 'bi-weekly'] as $key)
    {{-- @if($key === 'one-time' && (session()->get('amount-step')['frequency'] === 'one-time' || session()->get('amount-step')['frequency'] === 'both')) --}}
    @if($key === 'one-time' && ($frequency === 'one-time' || $frequency === 'both'))
        @php $key_ = $key; @endphp
        @php $keyCase = 'oneTime'; @endphp
        @include('annual-campaign.partials.amount-distribution')
    @endif
    {{-- @if($key === 'bi-weekly' && (session()->get('amount-step')['frequency'] === 'bi-weekly' || session()->get('amount-step')['frequency'] === 'both')) --}}
    @if($key === 'bi-weekly' && ($frequency === 'bi-weekly' || $frequency === 'both'))
        @php $key_ = $key;@endphp
        @php $keyCase = 'biWeekly'; @endphp
        @include('annual-campaign.partials.amount-distribution')
    @endif
@endforeach

