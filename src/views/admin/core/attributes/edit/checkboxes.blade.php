<div class="form-group @if (isset($invalid_fields[$attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}">{{ $attribute_name }} @if ($is_required) * @endif</label>

	@foreach ($attribute_definition['option_list'] as $option_key => $option)
		<div class="checkbox">
			<label>
				<input type="checkbox" value="{{ $option_key }}" name="attributes[{{ $attribute_identifier }}][]" @if (in_array($option_key, $prefill_data)) checked="checked" @endif>
				{{ $option }}
			</label>
		</div>
	@endforeach

    @if (isset($invalid_fields[$attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields[$attribute_identifier] }}</span>
    @endif

</div>
