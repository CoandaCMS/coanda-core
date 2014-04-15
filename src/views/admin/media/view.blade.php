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

					<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('media') }}">Up to Media</a></p>


					@if ($media->present()->has_preview)
						<div class="row">
							<div class="col-md-8">
								<img src="{{ $media->present()->large_url }}" class="img-thumbnail">
							</div>
							<div class="col-md-4">

								<p><a href="{{ Coanda::adminUrl('media/download/' . $media->id) }}"><i class="fa fa-download"></i> Download</a></p>

								<table class="table table-striped">
									<tr>
										<td>Created</td>
										<td>{{ $media->present()->created_at }}</td>
									</tr>
									<tr>
										<td>File size</td>
										<td>{{ $media->present()->file_size }}</td>
									</tr>
									@if ($media->is_image)
										<tr>
											<td>Dimensions</td>
											<td>0000 x 0000</td>
										</tr>
									@endif
									<tr>
										<td>Mime type</td>
										<td>{{ $media->present()->mime }}</td>
									</tr>
								</table>
							</div>
						</div>
					@endif

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

				</div>
			</div>
		</div>
	</div>
</div>

@stop
