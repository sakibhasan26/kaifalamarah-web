@if (isset($label))
    @php
        $for_id = preg_replace('/[^A-Za-z0-9\-]/', '', strip_tags(Str::lower($label)));
    @endphp
    <label for="{{ $for_id ?? "" }}">{!! $label !!}</label>
@endif

<input type="{{ $type ?? "text" }}" placeholder="{{ $placeholder ?? __('Type Here...') }}" name="{{ $name ?? "" }}" class="form--control {{ $class ?? "" }} @error($name ?? false) is-invalid @enderror" {{ $attribute ?? "" }} value="{{ $value ?? "" }}" @isset($data_limit)
    data-limit = {{ $data_limit }}
@endisset @isset($required)
    required
@endisset>
@isset($eye_button)
    {!! $eye_button !!}
@endisset
@isset($erroer_block)
@else
@error($name ?? false)
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
@endisset
