@if (isset($existing_permissions['everything']) && in_array('*', $existing_permissions['everything']))
	<p><i class="fa fa-check"></i> Full permissions.</p>
@else
	<table class="table table-striped seventy-30">
		@foreach ($permissions as $module_key => $module)
			<tr>
				<td>{{ $module['name'] }}</td>
				<td>

					<p>
						@if (isset($existing_permissions[$module_key]) && in_array('*', $existing_permissions[$module_key]))
							<i class="fa fa-check"></i> Everything
						@else
							<span class="disabled"><i class="fa fa-times"></i> Everything</span>
						@endif
					</p>

					@foreach ($module['views'] as $view_identifier => $view)

						@if (count($view['options']) > 0)

							<div class="row">

								<div class="col-xs-3">
									{{ $view['name'] }}
								</div>

								<div class="col-xs-9">
									@foreach ($view['options'] as $option_identifier => $option)

										<p>
											@if (isset($existing_permissions[$module_key][$view_identifier]) && in_array($option_identifier, $existing_permissions[$module_key][$view_identifier]))
												<i class="fa fa-check"></i> {{ $option }}
											@else
												<span class="disabled"><i class="fa fa-times"></i> {{ $option }}</span>
											@endif
										</p>

									@endforeach
								</div>
							</div>

						@else

							<p>
								@if (isset($existing_permissions[$module_key]) && in_array($view_identifier, $existing_permissions[$module_key]))
									<i class="fa fa-check"></i> {{ $view['name'] }}
								@else
									<span class="disabled"><i class="fa fa-times"></i> {{ $view['name'] }}</span>
								@endif
							</p>

						@endif

					@endforeach
				</td>
			</tr>
		@endforeach
	</table>



@endif
