@extends('coanda::admin.layout.main')

@section('page_title', 'View page')

@section('content')

<div class="container">
	<h1>{{ $page->name }}</h1>

	<p>Type: {{ $page->type_name }}</p>
	<p>Version: {{ $page->current_version }}</p>
	<p>Status: {{ $page->status }}</p>

	@foreach ($page->attributes as $attribute)

		@include('coanda::admin.pages.pageattributetypes.view.' . $attribute->type, [ 'attribute' => $attribute ])

	@endforeach

	<a href="{{ Coanda::adminUrl('pages/edit/' . $page->id) }}" class="btn btn-primary">New version</a>

	<h2>Versions</h2>

	@foreach ($page->versions as $version)
		<p>{{ $version->version }}, {{ $version->status }}, {{ $version->created_by }}, last updated {{ $version->updated_at }} @if ($version->status == 'draft') <a href="{{ Coanda::adminUrl('pages/editversion/' . $page->id . '/' . $version->version) }}" class="btn btn-primary">Edit</a> @endif </p>
	@endforeach
</div>

@stop
