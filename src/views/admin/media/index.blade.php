@extends('coanda::admin.layout.main')

@section('page_title', 'Media')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('media') }}">Media</a></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Media</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<a href="{{ Coanda::adminUrl('media/add') }}" class="btn btn-primary">Add media</a>
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

					@if (Session::has('media_uploaded'))
						<div class="alert alert-success">
							{{ Session::get('media_uploaded_message') }} successfully uploaded.
						</div>
					@endif

					@if ($media_list->count() > 0)
						<div class="row">
							@foreach ($media_list as $media)
								<div class="col-md-2 col-xs-3">
									<div class="thumbnail">
										<a href="{{ Coanda::adminUrl('media/view/' . $media->id) }}">
											@if ($media->present()->has_preview)
												<img src="{{ $media->present()->thumbnail_url }}" width="100" height="100">
											@else
												<img src="{{ asset('packages/coanda/images/file.png') }}" width="100" height="100">
											@endif
										</a>
										<div class="caption"><a href="{{ Coanda::adminUrl('media/view/' . $media->id) }}">{{ $media->present()->name }}</a></div>
									</div>
								</div>
							@endforeach
						</div>

						{{ $media_list->links() }}
					@else
						<p>No media in the system yet, be the first to upload!</p>
					@endif

				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#upload" data-toggle="tab">Upload</a></li>
				<li><a href="#search" data-toggle="tab">Search</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="upload">
					<p>Max file size <span class="label label-info">{{ ini_get('upload_max_filesize') }}</span></p>
					<form action="{{ Coanda::adminUrl('media/handle-upload') }}" class="dropzone" id="dropzone-uploader" data-reload-url="{{ Coanda::adminUrl('media') }}"></form>
				</div>
				<div class="tab-pane" id="search">
					Search media
				</div>
			</div>
		</div>
	</div>
</div>

@stop
