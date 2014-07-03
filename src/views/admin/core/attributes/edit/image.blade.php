{{--
$attribute_name
$attribute_identifier
$invalid_fields
$is_required
$prefill_data
--}}
<div class="form-group @if (isset($invalid_fields[$attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}_file">{{ $attribute_name }} @if ($is_required) * @endif</label>

	@if ($prefill_data)
		<div>
			<img src="{{ url($prefill_data->resizeUrl(150)) }}" width="150" class="img-thumbnail">
			<input type="hidden" name="attributes[{{ $attribute_identifier }}][media_id]" value="{{ $prefill_data->id }}">
		</div>
	@endif

	<input type="file" name="attribute_{{ $attribute_identifier }}_file" id="attribute_{{ $attribute_identifier }}_file">

    @if (isset($invalid_fields[$attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields[$attribute_identifier] }}</span>
    @endif

</div>
