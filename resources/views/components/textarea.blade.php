@props(['disabled' => false, 'showError' => true, 'label' => '', 'value' => '', 'info'=> ''])

<label>
    {{ __($label) }}
    @if ($info != '')
    <small class="text-muted">
        {{ __($info) }}
    </small>
    @endif
    <textarea {!! $attributes->merge(['class' => 'form-control']) !!}>{{$value}}</textarea>
    @if ($showError !== 'false') 
        <small class="text-danger">{{ $errors->first($attributes['name']) }}</small>
    @endif
</label>