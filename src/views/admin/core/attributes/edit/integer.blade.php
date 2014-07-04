<div class="form-group @if (isset($invalid_fields[$attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}">{{ $attribute_name }} @if ($is_required) * @endif</label>
    <input type="text" class="form-control" id="attribute_{{ $attribute_identifier }}" name="attributes[{{ $attribute_identifier }}]" value="{{ ($old_input ? $old_input : $prefill_data) }}">

    @if (isset($invalid_fields[$attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields[$attribute_identifier] }}</span>
    @endif

</div>
