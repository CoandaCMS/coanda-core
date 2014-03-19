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

	{{ Form::open(['url' => Coanda::adminUrl('pages/edit/' . $version->page_id . '/' . $version->version)]) }}

		@foreach ($version->attributes as $attribute)

			@include('coanda::admin.pages.pageattributetypes.' . $attribute->type, [ 'attribute' => $attribute ])

		@endforeach

		{{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-primary']) }}

	{{ Form::close() }}
</div>

@stop
