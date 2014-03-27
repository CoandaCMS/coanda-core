@extends('coanda::admin.layout.main')

@section('page_title', 'View page: ' . $page->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>

			@foreach ($page->getParents() as $parent)
				<li>
					<a href="{{ Coanda::adminUrl('pages/view/' . $parent->id) }}">{{ $parent->present()->name }}</a>
					{{--
					&nbsp;&nbsp;
					<a href="#sub-pages-{{ $parent->id }}" class="expand"><i class="fa fa-caret-square-o-down"></i></a>
					--}}
				</li>	
			@endforeach
			<li>{{ $page->present()->name }}</li>
		</ul>

		{{--
		@foreach ($page->getParents() as $parent)
			<div class="sub-pages-expand" id="sub-pages-{{ $parent->id }}">
				
				<p>Loading <span class="one">.</span><span class="two">.</span><span class="three">.</span></p>

			</div>
		@endforeach
		--}}
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">{{ $page->present()->name }} <small>{{ $page->present()->type }}</small></h1>
		<div class="page-status pull-right">
			<span class="label label-default">Version {{ $page->current_version }}</span>
			<span class="label @if ($page->status == 'Draft') label-warning @else label-success @endif">{{ $page->present()->status }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<div class="btn-group">
			<a href="{{ Coanda::adminUrl('pages/edit/' . $page->id) }}" class="btn btn-primary">New version</a>
			<div class="btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					More
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="#">More option 1</a></li>
					<li><a href="#">More option 2</a></li>
					<li><a href="#">More option 3</a></li>
				</ul>
			</div>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				Add sub page <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				@foreach (Coanda::availablePageTypes() as $page_type)
					<li><a href="{{ Coanda::adminUrl('pages/create/' . $page_type->identifier . '/' . $page->id) }}">{{ $page_type->name }}</a></li>
				@endforeach
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#subpages" data-toggle="tab">Sub pages</a></li>
				<li><a href="#content" data-toggle="tab">Content</a></li>
				<li><a href="#versions" data-toggle="tab">Versions</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="subpages">

					@if ($page->parent)
						<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages/view/' . $page->parent->id) }}">Up to {{ $page->parent->present()->name }}</a></p>
					@else
						<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages') }}">Up to Pages</a></p>
					@endif

					@if ($page->children->count() > 0)
						<table class="table table-striped">
						@foreach ($page->children as $child)
							<tr class="status-{{ $child->status }}">
								<td>
									@if ($child->status == 'draft')
										<i class="fa fa-circle-o"></i>
									@else
										<i class="fa fa-circle"></i>
									@endif
									<a href="{{ Coanda::adminUrl('pages/view/' . $child->id) }}">{{ $child->present()->name }}</a>
								</td>
								<td>{{ $child->present()->type }}</td>
								<td>{{ $child->children->count() }} sub page{{ $child->children->count() !== 1 ? 's' : '' }}</td>
								<td>{{ $child->present()->status }}</td>
							</tr>
						@endforeach
						</table>
					@else
						<p>This page doesn't have any sub pages</p>
					@endif
				</div>
				<div class="tab-pane" id="content">

					@foreach ($page->attributes as $attribute)

						@include('coanda::admin.pages.pageattributetypes.view.' . $attribute->type, [ 'attribute' => $attribute ])

					@endforeach

				</div>
				<div class="tab-pane" id="versions">

					<table class="table table-striped">
						@foreach ($page->versions as $version)
							<tr>
								<td>#{{ $version->version }}</td>
								<td>{{ $version->status }}</td>
							</tr>
						@endforeach
					</table>

				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#history" data-toggle="tab">History</a></li>
				<li><a href="#other" data-toggle="tab">Other</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="history">
					<div class="page-timeline">
						@foreach (range(1, 20) as $tmp)
							<div class="media">
								<img class="pull-left media-object img-circle" width="32" src="https://avatars2.githubusercontent.com/u/1886367?s=460">
								<div class="media-body">
									Edited
									<span class="pull-right">3 days ago</span>
								</div>
							</div>
						@endforeach
					</div>
				</div>
				<div class="tab-pane" id="other">
					Something else
				</div>
			</div>
		</div>
	</div>
</div>

@stop
