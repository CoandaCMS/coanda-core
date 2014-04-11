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

					<div class="row">
						@foreach ($media_list as $media)
							<div class="col-xs-6 col-md-3">
								<a href="#" class="thumbnail">
									<img data-src="holder.js/100%x180">
									<div class="caption">{{ $media->original_filename }}</div>
								</a>
							</div>
						@endforeach
					</div>

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
					<form action="{{ Coanda::adminUrl('media/handle-upload') }}" class="dropzone" id="my-awesome-dropzone"></form>
				</div>
				<div class="tab-pane" id="search">
					Search media
				</div>
			</div>
		</div>
	</div>
</div>

@stop
