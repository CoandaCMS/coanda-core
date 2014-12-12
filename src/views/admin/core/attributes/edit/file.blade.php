{{--
$attribute_name
$attribute_identifier
$invalid_fields
$is_required
$prefill_data
--}}
<div class="form-group @if (isset($invalid_fields[$attribute_identifier])) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute_identifier }}_file">{{ $attribute_name }} @if ($is_required) * @endif</label>

	@if ($prefill_data)
	    <p id="#file_preview_{{ $attribute_identifier }}">{{ $prefill_data->original_filename }}</p>
	    <input type="hidden" id="existing_file_id_{{ $attribute_identifier }}" name="attributes[{{ $attribute_identifier }}][media_id]" value="{{ $prefill_data->id }}">

        <a href="#" style="margin: 0 0 10px 0;" name="remove_file" id="remove_file_{{ $attribute_identifier }}" class="btn btn-danger btn-sm">Remove</a>
	@endif

	<input type="file" name="attribute_{{ $attribute_identifier }}_file" id="attribute_{{ $attribute_identifier }}_file">

    @if (isset($invalid_fields[$attribute_identifier]))
    	<span class="help-block">{{ $invalid_fields[$attribute_identifier] }}</span>
    @endif

</div>

@section('footer')
<script type="text/javascript">
$('#remove_file_{{ $attribute_identifier }}').on('click', function () {

	$('#existing_file_id_{{ $attribute_identifier }}').attr('value', 0);
	$('#file_preview_{{ $attribute_identifier }}').remove();

	$(this).remove();

	return false;

});
</script>
@append