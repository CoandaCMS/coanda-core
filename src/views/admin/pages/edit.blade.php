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
		<h1 class="pull-left">Edit page (version #{{ $version->version }}) <small>{{ $version->page->present()->type }}</small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('pages/editversion/' . $version->page_id . '/' . $version->version)]) }}
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

			<div class="page-tabs">

				<ul class="nav nav-tabs">
					<li class="active"><a href="#attributes" data-toggle="tab">Content</a></li>
					{{-- <li><a href="#variations" data-toggle="tab">Variations</a></li> --}}
					<li><a href="#layout" data-toggle="tab">Layout</a></li>

					@if ($version->page->show_meta)
						<li><a href="#meta" data-toggle="tab">Meta</a></li>
					@endif
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="attributes">
						@foreach ($version->attributes as $attribute)

							@include('coanda::admin.pages.pageattributetypes.edit.' . $attribute->type, [ 'attribute' => $attribute, 'invalid_fields' => $invalid_fields ])

							@if ($attribute->generates_slug)
								@section('footer')
									<script type="text/javascript">
										$('#slug').slugify('#attribute_{{ $attribute->id }}');
									</script>
								@append
							@endif

						@endforeach
					</div>

					{{--
					<div class="tab-pane" id="variations">

						Available variations e.g. with image/with date etc

					</div>
					--}}

					<div class="tab-pane" id="layout">

						<div class="form-group">
							<label class="control-label" for="layout">Layout</label>

							{{--
							<div class="row">
								<div class="col-xs-10">
									<select name="layout" id="layout" class="form-control">
										@foreach ($layouts as $layout)
											<option value="{{ $layout->identifier() }}">{{ $layout->name() }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-2">
									{{ Form::button('Customise', ['name' => 'customise_layout', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default btn-block']) }}
								</div>
							</div>
							--}}
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

			</div>
			<div class="buttons">
				{{ Form::button('Save', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Save & Exit', ['name' => 'save_exit', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Discard draft', ['name' => 'discard', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Publish', ['name' => 'publish', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-success']) }}
			</div>

		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label class="control-label" for="preview">Preview URL</label>
				<div class="input-group">
					<input type="text" class="form-control select-all" id="preview" name="preview" value="{{ url($version->present()->preview_url) }}" readonly>
					<span class="input-group-addon"><a class="new-window" href="{{ url($version->present()->preview_url) }}"><i class="fa fa-share-square-o"></i></a></span>
				</div>
			</div>

			@if (!$version->page->is_home)
				<div class="form-group @if (isset($invalid_fields['slug'])) has-error @endif">
					<label class="control-label" for="slug">Slug</label>

					<div class="input-group">
						<span class="input-group-addon">{{ $version->base_slug == '' ? '/' : $version->base_slug }}</span>
				    	<input type="text" class="form-control" id="slug" name="slug" value="{{ Input::old('slug', $version->slug) }}">
					</div>

				    @if (isset($invalid_fields['slug']))
				    	<span class="help-block">{{ $invalid_fields['slug'] }}</span>
				    @endif
				</div>

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
			@endif

			<div class="publish-options">
				@if (count($publish_handlers) > 1)

					@if (Session::has('missing_publish_handler'))
						<div class="alert alert-danger">
							Please choose how you would like this page to be published.
						</div>
					@endif

					<h2>Publish options</h2>
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

		</div>
	</div>
</div>
{{ Form::close() }}

@stop
