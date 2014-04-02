@extends('coanda::admin.layout.main')

@section('page_title', 'Existing drafts')

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
		<h1 class="pull-left">Choose drafts for "<a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->present()->name }}</a>"</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="edit-container">

	<div class="alert alert-warning">
		You already have drafts for this page
	</div>

	<div class="row">
		<div class="col-md-12">

			<table class="table table-striped">
				@foreach ($drafts as $version)
					<tr>
						<td class="tight">#{{ $version->version }}</td>
						<td>Last updated {{ $version->present()->updated_at }}</td>
						<td class="tight">
							@if ($version->status == 'draft')
								<a href="{{ Coanda::adminUrl('pages/editversion/' . $page->id . '/' . $version->version) }}"><i class="fa fa-pencil-square-o"></i></a>
							@endif
						</td>
					</tr>
				@endforeach
			</table>

			{{ Form::open(['url' => Coanda::adminUrl('pages/existing-drafts/' . $page->id)]) }}
				{{ Form::button('Serioulsy dude, just create a new version for me', ['name' => 'new_version', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
				<a class="btn btn-default" href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">Cancel</a>
			{{ Form::close() }}
		</div>
	</div>
</div>

@stop
