<select name="category">
    @foreach($categories as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>