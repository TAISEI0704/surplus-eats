{{-- <select name="category">
    <option value="">{{ __('Select Category') }}</option>
    <option value="all">All</option> --}}
    <optgroup label="{{ __('DISHES') }}">
    @foreach($categories_1 as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
    </optgroup>
    <optgroup label="{{ __('INGREDIENTS') }}">
    @foreach($categories_2 as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
    </optgroup>
{{-- </select> --}}