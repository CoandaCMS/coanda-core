{{--
$attribute_name
$attribute_identifier
$invalid_fields
$is_required
$prefill_data
--}}
<div class="form-group @if (isset($invalid_fields['attribute_' . $attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}">{{ $attribute_name }} @if ($is_required) * @endif</label>

	@if ($prefill_data)
		<div>
			<img src="{{ $prefill_data->present()->thumbnail_url }}" class="img-thumbnail">
			<input type="hidden" name="attribute_{{ $attribute_identifier }}_media_id" value="{{ $prefill_data->id }}">
		</div>
	@endif

	<input type="file" name="attribute_{{ $attribute_identifier }}" id="attribute_{{ $attribute_identifier }}">

    @if (isset($invalid_fields['attribute_' . $attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields['attribute_' . $attribute_identifier]['message'] }}</span>
    @endif

</div>
