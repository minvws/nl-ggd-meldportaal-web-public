@props(['field'])

@if(Lang::has("$field-explanation"))
<p class="explanation"
  data-open-label="@lang('Explanation for field:') @lang($field)"
  data-close-label="@lang('Close explanation for field:') @lang($field)"
  id="{{$field.'-explanation'}}"
  role="group"
  aria-label="@lang('Explanation')"
><span>@lang("Explanation"):</span> @lang("$field-explanation")</p>
@endif
