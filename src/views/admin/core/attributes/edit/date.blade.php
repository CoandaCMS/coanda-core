{{--
$attribute_name
$attribute_identifier
$invalid_fields
$is_required
$prefill_data
--}}
<div class="form-group @if (isset($invalid_fields['attribute_' . $attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}">{{ $attribute_name }} @if ($is_required) * @endif</label>

	<input type="hidden" name="attribute_{{ $attribute_identifier }}[format]" value="d/m/Y">

	<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY" data-hide-time="true">
		<input type="text" class="date-field form-control" id="attribute_{{ $attribute_identifier }}_date" name="attribute_{{ $attribute_identifier }}[date]" value="{{ isset(Input::old('attribute_' . $attribute_identifier, $prefill_data)['date']) ? Input::old('attribute_' . $attribute_identifier, $prefill_data)['date'] : '' }}">
		<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
	</div>

    @if (isset($invalid_fields['attribute_' . $attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields['attribute_' . $attribute_identifier]['message'] }}</span>
    @endif

</div>
