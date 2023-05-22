<div id="div_{{$id}}" class="@if ($hidden??false)hidden @endif" >
    @if ($required)
        <label for="{{$id}}">@lang('label_' . $id) <sup>*</sup></label>
    @else
        <label for="{{$id}}">@lang('label_' . $id)</label>
    @endif

    <div>
        <div>
            @foreach (json_decode($options) as $value => $label)
                <input type="checkbox" id="{{$value}}" value="{{$value}}" @if (in_array($value, old('data')[$name] ?? [])) checked @endif name="data[{{$name}}][]">
                <label for="{{$value}}">@if ($notranslate ?? false) {{$label}} @else @lang($label) @endif</label>
            @endforeach
        </div>

        <x-form-explanation :field="'{{$id}}'"/>
        @if ($errors->has('data.'.$name))
        <p class="error" role="status" id="error_{{$id}}" error-label="">{{__($errors->first('data.'.$name))}}</p>
        @endif

    </div>
</div>
