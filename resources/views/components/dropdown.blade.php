@props(['list' => [], 'label', 'selected' => null])
<label>
    {{ __($label ?? '') }}
    <select {!! $attributes->merge(['class' => 'form-control']) !!}>
        @foreach ($list as $item)
        <option value="{{ $item['id'] }}" {{$selected != null && ($item['id'] == $selected || (is_array($selected) && (in_array($item['id'], $selected)))) ? 'selected': '' }}>{{ $selected }}</option>
        @endforeach
    </select>
</label>