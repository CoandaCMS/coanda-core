{{--
$attribute_name
$attribute_identifier
$attribute_definition
$invalid_fields
$is_required
$prefill_data
--}}
<div class="form-group @if (isset($invalid_fields[$attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}">{{ $attribute_name }} @if ($is_required) * @endif</label>
    <textarea class="form-control" id="attribute_{{ $attribute_identifier }}" name="attributes[{{ $attribute_identifier }}]" {{ (isset($attribute_definition['max_length'][0]) ? 'maxlength="'.$attribute_definition['max_length'][0].'"' : '') }}>{{ ($old_input ? $old_input : $prefill_data) }}</textarea>

    @if (isset($invalid_fields[$attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields[$attribute_identifier] }}</span>
    @endif

</div>
