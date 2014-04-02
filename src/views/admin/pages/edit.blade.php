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
		<h1 class="pull-left">Edit page</h1>
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

			@endforeach

			{{ Form::button('Save', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
			{{ Form::button('Publish', ['name' => 'publish', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-success']) }}
			{{ Form::button('Generate Preview URL', ['name' => 'preview', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
			{{ Form::button('Discard draft', ['name' => 'discard', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
		</div>
		<div class="col-md-4">
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
