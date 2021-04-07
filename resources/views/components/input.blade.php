@props(['disabled' => false, 'showError' => true, 'label' => '', 'info' => ''])
<label>
    {{ __($label) }}
    @if ($info != '')
    <small class="text-muted">
        {{ __($info) }}
    </small>
    @endif
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control']) !!}>
    @if ($showError !== 'false') 
        <small class="text-danger">{{ $errors->first($attributes['name']) }}</small>
    @endif
</label>
