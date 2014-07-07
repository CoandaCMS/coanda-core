@extends('coanda::admin.layout.main')

@section('page_title', 'Tag: ' . $tag->tag)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('media') }}">Media</a></li>
			<li>{{ $tag->tag }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Tag: {{ $tag->tag }}</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ $media_list->getTotal() }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#media" data-toggle="tab">Media</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="media">

					<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('media') }}">Up to Media</a></p>

					@if ($media_list->count() > 0)
						<div class="row">
							@foreach ($media_list as $media)
								<div class="col-md-2 col-xs-3">
									<div class="thumbnail">
										<a href="{{ Coanda::adminUrl('media/view/' . $media->id) }}">
											@if ($media->type == 'image')
												<img src="{{ url($media->cropUrl(100)) }}" width="100" height="100">
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
						<p>No media taged with "{{ $tag->tag }}"</p>
					@endif

				</div>
			</div>
		</div>
	</div>
</div>

@stop
