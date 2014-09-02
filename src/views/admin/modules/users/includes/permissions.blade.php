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

					@if (isset($view['options']) && count($view['options']) > 0)

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
						@if (isset($view['location_paths']) && $view['location_paths'] == true)

							<div class="form-group">
								<label class="form-label">Allowed Locations</label>

								<div class="form-group existing-locations">
									@if (isset($existing_permissions[$module_key]['allowed_paths']))
										@foreach ($existing_permissions[$module_key]['allowed_paths'] as $path)
											@set('location', Coanda::pages()->locationByPath($path))

											<div class="input-group">
												<input type="hidden" name="permissions[{{ $module_key }}][allowed_paths][]" value="{{ $path }}">
												<input class="form-control" disabled="disabled" type="text" value="{{ $location ? $location->present()->path : '* Location not found *' }}">
												<span class="input-group-addon remove-location"><i class="fa fa-minus-circle"></i></span>
											</div>
										@endforeach
									@endif
								</div>

								<button class="btn btn-default" type="button" name="add_location"><span class="glyphicon glyphicon-plus"></span> Add location</button>
							</div>

						@else
							<div class="checkbox">
								<label>
									<input type="checkbox" name="permissions[{{ $module_key }}][]" value="{{ $view_identifier }}" {{ (isset($existing_permissions[$module_key]) && in_array($view_identifier, $existing_permissions[$module_key])) ? ' checked="checked"' : '' }}>
									{{ $view['name'] }}
								</label>
							</div>
						@endif
					@endif

				@endforeach
			</td>
		</tr>
	@endforeach
</table>

<div class="modal fade" id="add_location_modal" tabindex="-1" role="dialog" aria-labelledby="add_location_modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        		<h4 class="modal-title">Add location</h4>
      		</div>
      		<div class="modal-body">
				<div id="location-list">
					<div class="loading">Loading...</div>
				</div>
			</div>
		</div>
	</div>
</div>

@section('custom-js')
<script type="text/javascript">

var location_list_url = '{{ Coanda::adminUrl('pages/location-list-json/') }}';

$('button[name=add_location]').on('click', function () {

	$('#add_location_modal').modal('show');

	get_location_list(0);

});

$('.remove-location').on('click', function () {

	remove_location($(this));

});

function remove_location(element)
{
	element.parents('.input-group').remove();
}

function add_new_location(name, path)
{
	var group = $('<div />').addClass('input-group');
	var input = $('<input />')
					.attr('type', 'hidden')
					.attr('name' , 'permissions[pages][allowed_paths][]')
					.attr('value', path);

	input.appendTo(group);

	var name_value = $('<textarea />').html(name).text();
	var name = $('<input />')
					.attr('class', 'form-control')
					.attr('disabled', 'disabled')
					.attr('value', name_value);

	name.appendTo(group);

	var addon = $('<span class="input-group-addon"><i class="fa fa-minus-circle"></i></span>').on('click', function () {

		remove_location($(this));

	});

	addon.appendTo(group);

	group.appendTo($('.existing-locations'));
}

function get_location_list(location_id, page)
{
	if (!page)
	{
		page = 1;
	}

	$('#location-list').html('<div class="loading">Loading...</div>');

	$.get(location_list_url + '/' + location_id + '?page=' + page, function (result_data) {

		console.log(result_data);

		$('#location-list').empty();

		if (location_id != 0)
		{
			$('<div />')
				.html('<p><i class="fa fa-level-up"></i> <a href="#">Up</a></p>')
				.data('location_id', result_data.location ? result_data.location.parent_page_id : 0)
				.on('click', function (e) {

					e.preventDefault();

					get_location_list($(this).data('location_id'));

				})
				.appendTo($('#location-list'));			
		}

		if (result_data.sub_pages.data.length > 0)
		{
			var table = $('<table />').addClass('table').addClass('table-striped');

			$.each(result_data.sub_pages.data, function(index, value) {

				var path = (value.path == '' ? '/' : '') + value.path + value.id + '/';
				var row = $('<tr />');

				var td_left = $('<td />');

				$('<div />')
					.html('<a href="#">' + value.name + '</a>')
					.data('location_id', value.id)
					.on('click', function (e) {

						e.preventDefault();

						get_location_list($(this).data('location_id'));

					})
					.appendTo(td_left)

				td_left.appendTo(row);

				var td_right = $('<td />');

				$('<i />')
					.addClass('glyphicon glyphicon-plus-sign pull-right')
					.data('name', value.path_string)
					.data('path', path)
					.on('click', function () {
					
						add_new_location($(this).data('name'), $(this).data('path'))

						$('#add_location_modal').modal('hide');

					})
					.appendTo(td_right);

				td_right.appendTo(row);

				row.appendTo(table);

			});

			table.appendTo($('#location-list'));

			if (result_data.sub_pages.last_page > 1)
			{			
				// Add the pagination..
				var pagination_ul = $('<ul />').css({ borderTop: 'none', paddingTop: '10px' }).addClass('pager');

				var current_page = result_data.sub_pages.current_page;
				var last_page = result_data.sub_pages.last_page;

				$('<li />')
					.html('<a href="#">Previous</a>')
					.addClass(current_page == 1 ? 'disabled' : '')
					.addClass('previous')
					.data('location_id', result_data.location ? result_data.location.id : 0)
					.data('page', current_page == 1 ? false : (current_page - 1))
					.on('click', function (e) {

						e.preventDefault();

						if ($(this).data('page'))
						{
							get_location_list($(this).data('location_id'), $(this).data('page'));
						}

					})
					.appendTo(pagination_ul);

				$('<li />')
					.html('Page ' + current_page + '/' + last_page)
					.appendTo(pagination_ul);

				$('<li />')
					.html('<a href="#">Next</a>')
					.addClass(current_page == last_page ? 'disabled' : '')
					.addClass('next')
					.data('location_id', result_data.location ? result_data.location.id : 0)
					.data('page', current_page == last_page ? false : (current_page + 1))
					.on('click', function (e) {

						e.preventDefault();

						if ($(this).data('page'))
						{
							get_location_list($(this).data('location_id'), $(this).data('page'));
						}

					})
					.appendTo(pagination_ul);

				pagination_ul.appendTo($('#location-list'));
			}
		}
		else
		{
			$('<p />').html('No sub pages').appendTo($('#location-list'));
		}

	});

}

</script>
@append