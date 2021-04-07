@props(['list' => [], 'label'])
<label>
    {{ __($label ?? '') }}
    <select {!! $attributes->merge(['class' => 'form-control']) !!}>
        @foreach ($list as $item)
            <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
        @endforeach
    </select>
</label>