@extends('coanda::admin.layout.main')

@section('page_title', 'Viewing media: ' . $media->present()->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('media') }}">Media</a></li>
			<li>{{ $media->present()->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">{{ $media->present()->name }}</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		@if (Coanda::canView('media', 'remove'))
			<a href="{{ Coanda::adminUrl('media/remove/' . $media->id) }}" class="btn btn-danger">Delete</a>
		@else
			<span class="btn btn-primary" disabled="disabled">Delete</span>
		@endif
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#media" data-toggle="tab">Media</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="media">

					<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('media') }}">Up to Media</a></p>

					<div class="row">
						<div class="@if ($media->present()->has_preview) col-md-8 @else col-md-4 @endif">
							<a href="{{ Coanda::adminUrl('media/download/' . $media->id) }}">
								@if ($media->present()->has_preview)
									<img src="{{ url($media->present()->large_url) }}" class="img-thumbnail">
								@else
									<img src="{{ asset('packages/coanda/images/file.png') }}" width="200" height="200" class="img-thumbnail">
								@endif
							</a>
						</div>
						<div class="@if ($media->present()->has_preview) col-md-4 @else col-md-8 @endif">

							<p><a href="{{ Coanda::adminUrl('media/download/' . $media->id) }}"><i class="fa fa-download"></i> Download</a></p>

							<table class="table table-striped">
								<tr>
									<td>Created</td>
									<td>{{ $media->present()->created_at }}</td>
								</tr>
								<tr>
									<td>File size</td>
									<td>{{ $media->present()->size }}</td>
								</tr>
								@if ($media->type == 'image')
									<tr>
										<td>Dimensions</td>
										<td>{{ $media->present()->width }} x {{ $media->present()->height }}</td>
									</tr>
								@endif
								<tr>
									<td>Mime type</td>
									<td>{{ $media->present()->mime }}</td>
								</tr>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tag" data-toggle="tab">Tags</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tags">
					
					<p>
						@foreach ($tags as $tag)
							<i class="fa fa-tag"></i> {{ $tag->tag }}
							<a href="{{ Coanda::adminUrl('media/remove-tag/' . $media->id . '/' . $tag->id) }}"><i class="fa fa-times"></i></a>
							&nbsp;
						@endforeach
					</p>

					@if (Coanda::canView('media', 'tag'))

						{{ Form::open(['url' => Coanda::adminUrl('media/add-tag/' . $media->id), 'class' => 'form-inline']) }}

							<div class="form-group @if (Session::has('missing_file')) has-error @endif">
								<label for="tag" class="hidden">Tag</label>
								<input type="text" name="tag" class="form-control">
							</div>

							{{ Form::button('Add', ['name' => 'add_tag', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}

						{{ Form::close() }}
						
					@endif

				</div>
			</div>
		</div>
	</div>
</div>

@stop
