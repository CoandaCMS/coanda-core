@extends('coanda::admin.layout.main')

@section('page_title', 'Confirm remove')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('media') }}">Media</a></li>
			<li>Confirm removal</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Confirm removal <small>Media</small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('media/remove/' . $media->id)]) }}
<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#media" data-toggle="tab">Media</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="trashedpages">

					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete {{ $media->present()->name }}?
					</div>

					<p>
						@if ($media->present()->has_preview)
							<img src="{{ $media->present()->thumbnail_url }}" class="img-thumbnail">
						@endif
					</p>

					{{ Form::button('Yes, please remove this file', ['name' => 'remove', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
					<a class="btn btn-default" href="{{ Coanda::adminUrl('media/view/' . $media->id) }}">Cancel</a>

				</div>
			</div>
		</div>
	</div>
</div>
{{ Form::close() }}
@stop
