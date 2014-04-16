<div class="form-group @if (isset($invalid_fields['attribute_' . $attribute->id])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute->id }}">{{ $attribute->name }}</label>
    <textarea class="form-control" id="attribute_{{ $attribute->id }}" name="attribute_{{ $attribute->id }}">{{ $attribute->type_data }}</textarea>

    @if (isset($invalid_fields['attribute_' . $attribute->id]))
    	<span class="help-block">{{ $invalid_fields['attribute_' . $attribute->id] }}</span>
    @endif
</div>

@section('footer')
	<script type="text/javascript">

	var media_browse_url = '{{ Coanda::adminUrl('media/browse') }}';

	$('#attribute_{{ $attribute->id }}').summernote({
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'clear']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['picture', 'link', 'medialibrary', 'video']],
				['view', ['codeview']],
			],
		});
	</script>
@append
