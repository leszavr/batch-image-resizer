<div class="flex flex-wrap gap-2">
    @foreach($options as $key => $option)
        @php
            $value = is_string($key) ? $key : $option;
            $label = is_callable($labelFor) ? $labelFor($option, $mode) : $option;
            $isChecked = in_array($value, $selected, true);
        @endphp
        <label class="option-chip">
            <input type="checkbox" name="{{ $name }}[]" value="{{ $value }}" class="sr-only peer" @checked($isChecked)>
            <span class="option-chip__label">
                {{ $label }}
            </span>
        </label>
    @endforeach
</div>

