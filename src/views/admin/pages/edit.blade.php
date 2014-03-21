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
			Error!
		</div>
	@endif

	{{ Form::open(['url' => Coanda::adminUrl('pages/editversion/' . $version->page_id . '/' . $version->version)]) }}

		@foreach ($version->attributes as $attribute)

			@include('coanda::admin.pages.pageattributetypes.edit.' . $attribute->type, [ 'attribute' => $attribute, 'invalid' => Session::has('invalid_attributes') ? in_array($attribute->id, Session::get('invalid_attributes')) : false ])

		@endforeach

		{{ Form::button('Save', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
		{{ Form::button('Publish', ['name' => 'publish', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-success']) }}

	{{ Form::close() }}
</div>

@stop
