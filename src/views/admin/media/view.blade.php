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

					@if ($media->present()->has_preview)
						<img src="{{ $media->present()->large_url }}" class="thumbnail">
					@endif

					<a href="{{ Coanda::adminUrl('media/download/' . $media->id) }}">
						<i class="fa fa-download"></i> Download
					</a>

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
					List tags + add new
				</div>
			</div>
		</div>
	</div>
</div>

@stop
