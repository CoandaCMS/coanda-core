{{--
$attribute_name
$attribute_identifier
$invalid_fields
$is_required
$prefill_data
--}}
<div class="form-group @if (isset($invalid_fields[$attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}">{{ $attribute_name }} @if ($is_required) * @endif</label>
    <textarea class="form-control" id="attribute_{{ $attribute_identifier }}" name="attributes[{{ $attribute_identifier }}]">{{ ($old_input ? $old_input : $prefill_data) }}</textarea>

    @if (isset($invalid_fields[$attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields[$attribute_identifier] }}</span>
    @endif
</div>

@section('footer')
	<script type="text/javascript">

	var image_upload_url = '{{ Coanda::adminUrl('media/handle-upload') }}';

	function uploadImage(file, editor, welEditable)
	{
		data = new FormData();
	    data.append("file", file);

	    $.ajax({
	        data: data,
	        type: "POST",
	        url: image_upload_url,
	        cache: false,
	        contentType: false,
	        processData: false,
	        success: function(data)
	        {
                editor.insertImage(welEditable, data.original_file_url);
	        }
	    });
	}

	var media_browse_url = '{{ Coanda::adminUrl('media/browse') }}';

	$('#attribute_{{ $attribute_identifier }}').summernote({
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'clear']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'medialibrary', 'video']],
				['view', ['codeview']],
			],
			onImageUpload: function(files, editor, welEditable) {
    			
    			for (var i = 0; i < files.length; i ++)
    			{
    				uploadImage(files[i], editor, welEditable);	
    			}
  			}
		});
	</script>
@append
