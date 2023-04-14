{{-- <input type="hidden" name="last_distributions" value="{{ json_encode($selected_charities) }}"> --}}
<input type="hidden" name="last_selected_charities" value="{{ json_encode( isset($last_selected_charities) ? $last_selected_charities : [] ) }}">
<input type="hidden" name="last_one_time_amount" value="{{ isset($last_one_time_amount) ? $last_one_time_amount : '' }}">
<input type="hidden" name="last_bi_weekly_amount" value="{{ isset($last_bi_weekly_amount) ? $last_bi_weekly_amount : '' }}">


@foreach(['bi-weekly','one-time'] as $key)
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

