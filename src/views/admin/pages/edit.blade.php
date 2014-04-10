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
		<h1 class="pull-left">Edit page (version #{{ $version->version }})</h1>
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

			<div class="buttons">
				{{ Form::button('Save', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Save & Exit', ['name' => 'save_exit', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Discard draft', ['name' => 'discard', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
			</div>

			<div class="publish-options">

				@if (count($publish_handlers) > 1)
					<h2>Publish options</h2>
					@foreach ($publish_handlers as $publish_handler)
						<div class="row">
							<div class="col-sm-4">
								<div class="radio">
									<label>
										<input type="radio" name="publish_handler" value="{{ $publish_handler->identifier }}">
										{{ $publish_handler->name }}
									</label>
								</div>
							</div>
							<div class="col-sm-8">
								@include($publish_handler->template, [ 'publish_handler' => $publish_handler, 'publish_handler_invalid_fields' => $publish_handler_invalid_fields ])
							</div>
						</div>
					@endforeach
				@else
					<input type="hidden" name="publish_handler" value="{{ $publish_handlers[array_keys($publish_handlers)[0]]->identifier }}">
					@include($publish_handlers[array_keys($publish_handlers)[0]]->template, [ 'publish_handler' => $publish_handlers[array_keys($publish_handlers)[0]], 'publish_handler_invalid_fields' => $publish_handler_invalid_fields ])
				@endif
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

			@if ($version->page->show_meta)
				<fieldset>
					<legend>Meta</legend>

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
				</fieldset>
			@endif
		</div>
	</div>
</div>
{{ Form::close() }}

@stop
