<div class="form-group @if ($invalid) has-error @endif">
	<label class="control-label" for="attribute_{{ $attribute->id }}">{{ $attribute->name }}</label>
    <input type="text" class="form-control" id="attribute_{{ $attribute->id }}" name="attribute_{{ $attribute->id }}" value="{{ $attribute->type_data }}">
</div>
