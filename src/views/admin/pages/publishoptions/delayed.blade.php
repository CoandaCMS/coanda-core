<div class="form-group @if (isset($publish_handler_invalid_fields['delayed_publish_date'])) has-error @endif">
	<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY HH:mm">
		<input type="text" class="date-field form-control" id="delayed_publish_date" name="delayed_publish_date" value="{{ Input::old('delayed_publish_date') }}">
		<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
	</div>
	@if (isset($publish_handler_invalid_fields['delayed_publish_date']))
		<span class="help-block">{{ $publish_handler_invalid_fields['delayed_publish_date'] }}</span>
	@endif
</div>