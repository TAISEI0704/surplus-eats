{{-- <select name="category">
    <option value="">{{ __('Select Category') }}</option>
    <option value="all">All</option> --}}
    @foreach($categories as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
{{-- </select> --}}