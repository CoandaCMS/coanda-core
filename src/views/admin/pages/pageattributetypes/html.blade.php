<div class="form-group">
	<label for="attribute_{{ $attribute->id }}">{{ $attribute->name }}</label>
    <textarea class="form-control" id="attribute_{{ $attribute->id }}" name="attribute_{{ $attribute->id }}">{{ $attribute->type_data }}</textarea>
</div>

@section('footer')
	<script type="text/javascript">
	$('#attribute_{{ $attribute->id }}').summernote({
			height: 200
		});
	</script>
@append
