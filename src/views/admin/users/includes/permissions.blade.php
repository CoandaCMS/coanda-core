<div class="checkbox">
	<label class="permission-everything-option">
		<input type="checkbox" name="permissions[everything][]" value="*" {{ (isset($existing_permissions['everything']) && in_array('*', $existing_permissions['everything'])) ? ' checked="checked"' : '' }}>
		Everything
	</label>
</div>

<table class="table table-striped seventy-30">
	@foreach ($permissions as $module_key => $module)
		<tr>
			<td>{{ $module['name'] }}</td>
			<td>
				<div class="checkbox">
					<label class="permission-everything-option">
						<input type="checkbox" name="permissions[{{ $module_key }}][]" value="*" {{ (isset($existing_permissions[$module_key]) && in_array('*', $existing_permissions[$module_key])) ? ' checked="checked"' : '' }}>
						Everything
					</label>
				</div>

				@foreach ($module['views'] as $view_identifier => $view)

					@if (count($view['options']) > 0)

						<div class="row">

							<div class="col-xs-3">
								<div class="permission-option-label">{{ $view['name'] }}</div>
							</div>

							<div class="col-xs-9">
								@foreach ($view['options'] as $option_identifier => $option)
									<div class="checkbox">
										<label>
											<input type="checkbox" name="permissions[{{ $module_key }}][{{ $view_identifier }}][]" value="{{ $option_identifier }}" {{ (isset($existing_permissions[$module_key][$view_identifier]) && in_array($option_identifier, $existing_permissions[$module_key][$view_identifier])) ? ' checked="checked"' : '' }}>
											{{ $option }}
										</label>
									</div>
								@endforeach
							</div>
						</div>

					@else
						<div class="checkbox">
							<label>
								<input type="checkbox" name="permissions[{{ $module_key }}][]" value="{{ $view_identifier }}" {{ (isset($existing_permissions[$module_key]) && in_array($view_identifier, $existing_permissions[$module_key])) ? ' checked="checked"' : '' }}>
								{{ $view['name'] }}
							</label>
						</div>
					@endif

				@endforeach
			</td>
		</tr>
	@endforeach
</table>
