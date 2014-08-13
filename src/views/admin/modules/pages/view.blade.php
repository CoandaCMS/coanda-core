@extends('coanda::admin.layout.main')

@section('page_title', 'View page: ' . $page->present()->name)

@section('content')

<div class="row">

	<div class="breadcrumb-nav">

		<div class="pull-right">
			@if (Coanda::canView('pages', 'remove'))
				<a href="{{ Coanda::adminUrl('pages/trash') }}" class="trash-icon"><i class="fa fa-trash-o"></i> Trash</a>
			@else
				<span  class="trash-icon disabled"><i class="fa fa-trash-o"></i> Trash</span>
			@endif
		</div>

		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>{{ $page->present()->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			@if ($page->is_trashed) [Trashed] @endif
			{{ $page->present()->name }}
			<small>
				@if ($page->is_draft)
					<i class="fa fa-circle-o"></i>
				@else
					<i class="fa {{ $page->pageType()->icon() }}"></i>
				@endif
				{{ $page->present()->type }}
			</small>
		</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Version {{ $page->current_version }}</span>

			@if ($page->is_trashed)
				<span class="label label-danger">{{ $page->present()->status }}</span>
			@elseif ($page->is_pending)
				<span class="label label-info">{{ $page->present()->status }}</span>
			@else
				<span class="label @if ($page->is_draft) label-warning @else label-success @endif">{{ $page->present()->status }}</span>
			@endif
		</div>
	</div>
</div>

@if ($page->visible_from || $page->visible_to)
<div class="row">
	<div class="page-visibility col-md-12">
		@if ($page->is_visible)
			<span class="label label-success">Visible</span>
		@else
			<span class="label label-info">Hidden</span>
		@endif
		<i class="fa fa-calendar"></i> {{ $page->present()->visible_dates }}
	</div>
</div>
@endif

@if ($page->is_hidden || $page->is_hidden_navigation)
<div class="row">
	<div class="page-hidden col-md-12">
		@if ($page->is_hidden)
			<span class="label label-danger">Hidden</span>
		@endif
		@if ($page->is_hidden_navigation)
			<span class="label label-warning">Hidden from Navigation</span>
		@endif
	</div>
</div>
@endif

<div class="row">
	<div class="page-options col-md-12">
		<div class="btn-group">
			@if ($page->is_trashed)
				@if (Coanda::canView('pages', 'remove', ['page_id' => $page->id, 'page_type' => $page->type]))
					<a href="{{ Coanda::adminUrl('pages/restore/' . $page->id) }}" class="btn btn-primary">Restore</a>
				@else
					<span class="btn btn-primary" disabled="disabled">Restore</span>
				@endif
			@else
				@if ($page->is_draft)
					<a href="{{ Coanda::adminUrl('pages/editversion/' . $page->id . '/1') }}" class="btn btn-primary">Continue editing</a>
				@else
					@if (Coanda::canView('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]))
						<a href="{{ Coanda::adminUrl('pages/edit/' . $page->id) }}" class="btn btn-primary">Edit</a>
					@else
						<span class="btn btn-primary" disabled="disabled">Edit</span>
					@endif
				@endif
				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						More
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li>
							@if (Coanda::canView('pages', 'remove', ['page_id' => $page->id, 'page_type' => $page->type]))
								<a href="{{ Coanda::adminUrl('pages/delete/' . $page->id) }}">Delete</a>
							@else
								<span class="disabled">Delete</span>
							@endif
						</li>
					</ul>
				</div>
			@endif
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">

				<li class="active"><a href="#content" data-toggle="tab">Content</a></li>

				@if ($page->locations->count() > 0)
					<li><a href="#locations" data-toggle="tab">Locations ({{ $page->locations->count() }})</a></li>
				@endif
			</ul>
			<div class="tab-content">

				<div class="tab-pane active" id="content">

					<table class="table table-striped">
						@foreach ($page->attributes as $attribute)
						<tr>
							<td>{{ $attribute->name }}</td>
							<td>
								@include($attribute->type()->view_template(), [ 'attribute_definition' => $attribute->definition, 'content' => $attribute->type_data ])
							</td>
						</tr>
						@endforeach
					</table>

				</div>

				@if ($page->locations->count() > 0)
					<div class="tab-pane" id="locations">
						<table class="table table-striped">
							@foreach ($page->locations as $location)
							<tr>
								<td>
									<a href="{{ Coanda::adminUrl('pages/location/' . $location->id) }}">

										@foreach ($location->parents() as $parent)
											{{ $parent->page->present()->name }} /
										@endforeach

										{{ $location->page->present()->name }}
									</a>
								</td>
								<td>
									@if ($page->pageType()->allowsSubPages())
										{{ $location->children->count() }} sub page{{ $location->children->count() !== 1 ? 's' : '' }}
									@endif
								</td>
							</tr>
							@endforeach
						</table>
					</div>
				@endif

			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#contributors" data-toggle="tab">Contributors</a></li>
				<li><a href="#history" data-toggle="tab">History</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="contributors">
					<table class="table table-striped table-history">
						@foreach ($contributors as $contributor)
							<tr>
								<td class="tight"><img src="{{ $contributor->avatar }}" class="img-circle" width="25"></td>
								<td>{{ $contributor->present()->name }}</td>
							</tr>
						@endforeach
					</table>
				</div>
				<div class="tab-pane" id="history">
					<table class="table table-striped table-history">
						@foreach ($history as $history)
							<tr>
								<td class="tight"><img src="{{ $history->present()->avatar }}" class="img-circle" width="25"></td>
								<td>{{ $history->present()->username }}</td>
								<td>{{ $history->present()->happening }}</td>
								<td>{{ $history->present()->created_at }}</td>
							</tr>
						@endforeach
					</table>

					<a href="{{ Coanda::adminUrl('pages/history/' . $page->id)}}">View all history</a>
				</div>
			</div>
		</div>
	</div>
</div>

@stop
