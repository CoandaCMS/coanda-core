<div class="form-group @if (isset($invalid_fields['attribute_' . $attribute->id])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute->id }}">{{ $attribute->name }}</label>
    <textarea class="form-control" id="attribute_{{ $attribute->id }}" name="attribute_{{ $attribute->id }}">{{ $attribute->type_data }}</textarea>

    @if (isset($invalid_fields['attribute_' . $attribute->id]))
    	<span class="help-block">{{ $invalid_fields['attribute_' . $attribute->id] }}</span>
    @endif
</div>

@section('footer')
	<script type="text/javascript">
	$('#attribute_{{ $attribute->id }}').summernote({
			height: 200
		});
	</script>
@append
