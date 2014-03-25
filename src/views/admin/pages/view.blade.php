@extends('coanda::admin.layout.main')

@section('page_title', 'View page: ' . $page->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">

		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			@foreach ($page->getParents() as $parent)
				<li>
					<a href="{{ Coanda::adminUrl('pages/view/' . $parent->id) }}">{{ $parent->name }}</a>
					<span class="caret"></span>
				</li>	
			@endforeach
		</ul>

	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">

		<h1 class="pull-left">{{ $page->name }} <small>{{ $page->type_name }}</small></h1>

		<div class="page-status pull-right">
			<span class="label label-default">Version {{ $page->current_version }}</span>
			<span class="label @if ($page->status == 'Draft') label-warning @else label-success @endif">{{ $page->status }}</span>
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

					@if ($page->children->count() > 0)
						<table class="table table-striped">
						@foreach ($page->children as $child)
							<tr>
								<td><a href="{{ Coanda::adminUrl('pages/view/' . $child->id) }}">{{ $child->name == '' ? 'Not set' : $child->name }}</a></td>
								<td>{{ $child->type_name }}</td>
								<td>{{ $child->status }}</td>
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
