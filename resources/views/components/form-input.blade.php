<div id="div_{{$id}}" class="@if ($hidden??false)hidden @endif" >
    @if ($required)
        <label for="{{$id}}">@lang('label_' . $id) <sup>*</sup></label>
    @else
        <label for="{{$id}}">@lang('label_' . $id)</label>
    @endif

    <div>
        <input id="{{$id}}"  name="data[{{$name}}]" value="{{old('data')[$name] ?? $value ?? ''}}" type="{{$type??"text"}}"
           @if ($autocomplete??false)autocomplete=name @endif
           @if ($required) aria-required=true @endif
           @if ($errors->has('data.'.$name)) aria-invalid=true @endif
           @if (Lang::has("$id-explanation")) aria-describedby={{"$id-explanation"}} @endif
           @if ($placeholder ?? false) placeholder="{{$placeholder}}" @endif
        />
        <x-form-explanation field="{{$id}}"/>
        @if ($errors->has('data.'.$name))
        <p class="error" role="status" id="error_{{$id}}" error-label="">{{__($errors->first('data.'.$name))}}</p>
        @endif
    </div>
</div>
