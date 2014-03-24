@extends('coanda::admin.layout.main')

@section('page_title', 'Edit page')

@section('content')

<div class="container">
	<h1>Edit</h1>

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

	{{ Form::open(['url' => Coanda::adminUrl('pages/editversion/' . $version->page_id . '/' . $version->version)]) }}

		@foreach ($version->attributes as $attribute)

			@include('coanda::admin.pages.pageattributetypes.edit.' . $attribute->type, [ 'attribute' => $attribute, 'invalid_fields' => $invalid_fields ])

		@endforeach

		<div class="form-group @if (isset($invalid_fields['slug'])) has-error @endif">
			<label class="control-label" for="slug">Slug</label>

			<div class="input-group">
				<span class="input-group-addon">/parent-pages/</span>
		    	<input type="text" class="form-control" id="slug" name="slug" value="{{ Input::old('slug', $version->slug) }}">
			</div>

		    @if (isset($invalid_fields['slug']))
		    	<span class="help-block">{{ $invalid_fields['slug'] }}</span>
		    @endif
		</div>

		{{ Form::button('Save', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
		{{ Form::button('Publish', ['name' => 'publish', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-success']) }}

	{{ Form::close() }}
</div>

@stop
