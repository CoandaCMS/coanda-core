@extends('coanda::admin.layout.main')

@section('page_title', 'Edit page')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>Edit page</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			Edit page (version #{{ $version->version }})
			<small>
				<i class="fa {{ $version->page->pageType()->icon() }}"></i>
				{{ $version->page->present()->type }}
			</small>
		</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>


{{ Form::open(['url' => Coanda::adminUrl('pages/editversion/' . $version->page_id . '/' . $version->version), 'files' => true]) }}
<div class="edit-container">

	@if (Session::has('page_saved'))
		<div class="alert alert-success">
			Saved
		</div>
	@endif

	@if (Session::has('error'))
		<div class="alert alert-danger">
			Error
		</div>
	@endif

	<div class="row">
		<div class="col-md-8">

			<ul class="nav nav-tabs">
				<li @if (!Session::has('layout_chosen')) class="active" @endif><a href="#attributes" data-toggle="tab">Content</a></li>
				<li @if (Session::has('layout_chosen')) class="active" @endif><a href="#layout" data-toggle="tab">Layout</a></li>
				@if ($version->page->show_meta)
					<li><a href="#meta" data-toggle="tab">Meta</a></li>
				@endif
			</ul>

			<div class="tab-content edit-container">
				<div class="tab-pane @if (!Session::has('layout_chosen')) active @endif" id="attributes">
					@foreach ($version->attributes as $attribute)

						@include($attribute->type()->edit_template(), [ 'attribute_identifier' => $attribute->id, 'attribute_name' => $attribute->name, 'invalid_fields' => $invalid_fields, 'is_required' => $attribute->is_required, 'prefill_data' => $attribute->type_data ])

						@if ($attribute->generates_slug)
							@section('footer')
								<script type="text/javascript">
									$('#attribute_{{ $attribute->id }}').slugify('.slugiwugy');
								</script>
							@append
						@endif

					@endforeach
				</div>

				<div class="tab-pane @if (Session::has('layout_chosen')) active @endif" id="layout">

					@if (Session::has('layout_chosen'))
						<div class="alert alert-success">
							Layout chosen
						</div>
					@endif

					<div class="form-group">
						<label class="control-label" for="layout_identifier">Layout</label>

						<div class="row">
							<div class="col-xs-10">
								<select name="layout_identifier" id="layout_identifier" class="form-control">
									<option value=""></option>
									@foreach ($layouts as $layout)
										<option @if ($version->layout_identifier == $layout->identifier()) selected="selected" @endif value="{{ $layout->identifier() }}">{{ $layout->name() }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-xs-2">
								{{ Form::button('Choose', ['name' => 'choose_layout', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default btn-block']) }}
							</div>
						</div>

						@if ($version->layout_identifier)
							<div class="edit-container">
								<div class="panel-group" id="accordion">
									@foreach ($version->layout->regions() as $region)
										<div class="panel panel-default">
											<div class="panel-heading">
												<a data-toggle="collapse" data-parent="#accordion" href="#region_{{ $region->identifier() }}">{{ $region->name() }}</a>
											</div>
											<div id="region_{{ $region->identifier() }}" class="panel-collapse collapse">
												<div class="panel-body">

													@if (count($version->layoutRegionBlocks($region->identifier())) > 0)
														<table class="table table-striped">
															@foreach ($version->layoutRegionBlocks($region->identifier()) as $region_block)
																<tr>
																	<td class="tight"><a href="{{ Coanda::adminUrl('pages/remove-custom-region-block/' . $region_block->block->id . '/' . $version->page->id . '/' . $version->version) }}"><i class="fa fa-minus-circle"></i></a></td>
																	<td>{{ $region_block->block->name }}</td>
																	<td class="order-column">{{ Form::text('region_block_ordering[' . $region_block->id . ']', $region_block->order, ['class' => 'form-control input-sm']) }}</td>
																</tr>
															@endforeach
														</table>

														{{ Form::button('Update ordering', ['name' => 'update_region_block_order', 'value' => 'true', 'type' => 'submit', 'class' => 'pull-right btn btn-default']) }}
													@else
														<p>No custom blocks have been added, the defaults will be used.</p>
													@endif

													{{ Form::button('Add custom block', ['name' => 'add_custom_block', 'value' => $version->layout->identifier() . '/' . $region->identifier(), 'type' => 'submit', 'class' => 'btn btn-default']) }}

												</div>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						@endif

					</div>
				</div>

				@if ($version->page->show_meta)
					<div class="tab-pane" id="meta">
						<div class="form-group @if (isset($invalid_fields['meta_page_title'])) has-error @endif">
							<label class="control-label" for="meta_page_title">Page title</label>
					    	<input type="text" class="form-control" id="meta_page_title" name="meta_page_title" value="{{ Input::old('meta_page_title', $version->meta_page_title) }}">

						    @if (isset($invalid_fields['meta_page_title']))
						    	<span class="help-block">{{ $invalid_fields['meta_page_title'] }}</span>
						    @endif
						</div>
						<div class="form-group @if (isset($invalid_fields['meta_description'])) has-error @endif">
							<label class="control-label" for="meta_description">Meta description</label>
					    	<textarea type="text" class="form-control" id="meta_description" name="meta_description">{{ Input::old('meta_description', $version->meta_description) }}</textarea>

						    @if (isset($invalid_fields['meta_description']))
						    	<span class="help-block">{{ $invalid_fields['meta_description'] }}</span>
						    @endif
						</div>
					</div>
				@endif

			</div>

			<div class="buttons">
				{{ Form::button('Save', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Save & Exit', ['name' => 'save_exit', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Discard draft', ['name' => 'discard', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
			</div>

			<div class="publish-options">
				@if (count($publish_handlers) > 1)

					<h2>Publish options</h2>

					@if (Session::has('missing_publish_handler'))
						<div class="alert alert-danger">
							Please choose how you would like this page to be published.
						</div>
					@endif

					@foreach ($publish_handlers as $publish_handler)
						<div class="row">
							<div class="col-sm-6">
								<div class="radio">
									<label>
										<input type="radio" name="publish_handler" id="publish_handler_{{ $publish_handler->identifier }}" value="{{ $publish_handler->identifier }}" {{ (Input::old('publish_handler', $default_publish_handler) == $publish_handler->identifier) ? ' checked="checked"' : '' }}>
										{{ $publish_handler->name }}
									</label>
								</div>
							</div>
							<div class="col-sm-6">
								@include($publish_handler->template, [ 'publish_handler' => $publish_handler, 'publish_handler_invalid_fields' => $publish_handler_invalid_fields ])
							</div>
						</div>
					@endforeach
				@else
					<input type="hidden" name="publish_handler" value="{{ $publish_handlers[array_keys($publish_handlers)[0]]->identifier }}">
					@include($publish_handlers[array_keys($publish_handlers)[0]]->template, [ 'publish_handler' => $publish_handlers[array_keys($publish_handlers)[0]], 'publish_handler_invalid_fields' => $publish_handler_invalid_fields ])
				@endif
			</div>

			<div class="buttons">
				{{ Form::button('Publish', ['name' => 'publish', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-success']) }}
			</div>				

		</div>
		<div class="col-md-4">

			<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab">Details</a></li>
				<li><a href="#preview" data-toggle="tab">Preview @if ($version->comments->count() > 0) <span class="label label-info">{{ $version->comments->count() }} comments</span> @endif</a></li>
			</ul>

			<div class="edit-container tab-content">

				<div class="tab-pane active" id="details">
					@if (!$version->page->is_home)

						@if ($version->slugs()->count() > 0)
							@foreach ($version->slugs as $slug)
								<div class="form-group @if (isset($invalid_fields['slug_' . $slug->id])) has-error @endif">

									<div class="input-group">
										<span class="input-group-addon" style="overflow: hidden; max-width: 150px; text-align: right;">
											<span style="float: right;">{{ $slug->base_slug }}/</span>
										</span>
								    	<input type="text" class="form-control slugiwugy" id="slug_{{ $slug->id }}" name="slug_{{ $slug->id }}" value="{{ Input::old('slug_' . $slug->id, $slug->slug) }}">
								    	<span class="input-group-addon refresh-slug"><i class="fa fa-refresh"></i></span>
								    	<span class="input-group-addon"><input type="checkbox" name="remove_slug_list[]" value="{{ $slug->id }}"></span>
									</div>

								    @if (isset($invalid_fields['slug_' . $slug->id]))
								    	<span class="help-block">{{ $invalid_fields['slug_' . $slug->id] }}</span>
								    @endif

								</div>
							@endforeach
						@else
							<div class="form-group @if (isset($invalid_fields['slugs'])) has-error @endif">
								<p>No locations for this page.</p>

							    @if (isset($invalid_fields['slugs']))
							    	<span class="help-block">{{ $invalid_fields['slugs'] }}</span>
							    @endif
							</div>
						@endif

						<div class="form-group">
							{{ Form::button('Add location', ['name' => 'add_location', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default btn-sm']) }}
							{{ Form::button('Remove selected', ['name' => 'remove_locations', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default btn-sm pull-right']) }}
						</div>
					@endif

					<div class="row">
						<input type="hidden" name="date_format" value="d/m/Y H:i">
						<div class="col-md-6">
							<div class="form-group @if (isset($invalid_fields['visible_from'])) has-error @endif">
								<label class="control-label" for="visibility">Visibile from</label>
								<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY HH:mm">
									<input type="text" class="date-field form-control" id="visible_from" name="visible_from" value="{{ Input::old('visible_from', $version->present()->visible_from) }}">
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
								</div>
							    @if (isset($invalid_fields['visible_from']))
							    	<span class="help-block">{{ $invalid_fields['visible_from'] }}</span>
							    @endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group @if (isset($invalid_fields['visible_to'])) has-error @endif">
								<label class="control-label" for="visibility">Visibile to</label>
								<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY HH:mm">
									<input type="text" class="date-field form-control" id="visible_to" name="visible_to" value="{{ Input::old('visible_to', $version->present()->visible_to) }}">
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
								</div>
							    @if (isset($invalid_fields['visible_to']))
							    	<span class="help-block">{{ $invalid_fields['visible_to'] }}</span>
							    @endif
							</div>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="preview">
					<div class="form-group">
						<label class="control-label" for="preview">Preview URL</label>
						<div class="input-group">
							<input type="text" class="form-control select-all" id="preview" name="preview" value="{{ url($version->present()->preview_url) }}" readonly>
							<span class="input-group-addon"><a class="new-window" href="{{ url($version->present()->preview_url) }}"><i class="fa fa-share-square-o"></i></a></span>
						</div>
					</div>

					@if ($version->comments->count() > 0)
						<h2>Comments</h2>
						@foreach ($version->comments as $comment)
							<div class="well well-sm version-comment">
								<p class="lead">"{{ nl2br($comment->comment) }}"</p>
								<div class="pull-right">{{ $comment->present()->created_at }} from <strong>{{ $comment->name }}</strong></div>
								<div class="clearfix"></div>
							</div>
						@endforeach
					@endif

				</div>
			</div>

		</div>
	</div>
</div>
{{ Form::close() }}

@stop
