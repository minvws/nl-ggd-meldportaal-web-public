<div id="div_{{$id}}" class="@if ($hidden??false)hidden @endif" >
    @if ($required)
        <label for="{{$id}}">@lang('label_' . $id) <sup>*</sup></label>
    @else
        <label for="{{$id}}">@lang('label_' . $id)</label>
    @endif

    <div>
        <select id="{{$id}}" name="data[{{$name}}]"
            @if ($required) aria-required=true @endif
            @if ($errors->has('data.'.$name)) aria-invalid=true @endif
            @if (Lang::has("$id-explanation")) aria-describedby={{"$id-explanation"}} @endif
        >
            @if ($empty ?? false)
                <option value="" @selected(empty(old('data')[$name] ?? ''))>@lang('Choose an option')</option>
            @endif

            @foreach (json_decode($options) as $value => $label)
                <option value="{{$value}}" @if ((old('data')[$name] ?? $default ?? '') == $value) selected @endif >
                    @if ($notranslate ?? false) {{$label}} @else @lang($label) @endif
                </option>
            @endforeach
        </select>

        <x-form-explanation :field="'{{$id}}'"/>
        @if ($errors->has('data.'.$name))
        <p class="error" role="status" id="error_{{$id}}" error-label="">{{__($errors->first('data.'.$name))}}</p>
        @endif

    </div>
</div>
