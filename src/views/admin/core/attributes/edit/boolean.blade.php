{{--
$attribute_name
$attribute_identifier
$invalid_fields
$is_required
$prefill_data
--}}
<div class="form-group @if (isset($invalid_fields['attribute_' . $attribute_identifier])) has-error @endif">
    <div class="checkbox">
    	<label>
    		<input type="checkbox" name="attributes[{{ $attribute_identifier }}]" value="yes" @if ($prefill_data == 'yes') checked="checked" @endif>
    		{{ $attribute_name }}
    	</label>
    </div>

    @if (isset($invalid_fields['attribute_' . $attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields['attribute_' . $attribute_identifier]['message'] }}</span>
    @endif
</div>
