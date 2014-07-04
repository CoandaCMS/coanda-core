<div class="form-group @if (isset($invalid_fields[$attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}">{{ $attribute_name }} @if ($is_required) * @endif</label>

	<select id="attribute_{{ $attribute_identifier }}" name="attributes[{{ $attribute_identifier }}]" class="form-control">
		@foreach ($attribute_definition['option_list'] as $option_key => $option)
			<option @if ($prefill_data == $option_key) selected="selected" @endif value="{{ $option_key }}">{{ $option }}</option>
		@endforeach
	</select>

    @if (isset($invalid_fields[$attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields[$attribute_identifier] }}</span>
    @endif

</div>
