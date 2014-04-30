@extends('coanda::admin.layout.main')

@section('page_title', 'View page: ' . $page->name)

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

			@foreach ($page->parents() as $parent)
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
		@foreach ($page->parents() as $parent)
			<div class="sub-pages-expand" id="sub-pages-{{ $parent->id }}">
				
				<p>Loading <span class="one">.</span><span class="two">.</span><span class="three">.</span></p>

			</div>
		@endforeach
		--}}
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">@if ($page->is_trashed) [Trashed] @endif {{ $page->present()->name }} <small>{{ $page->present()->type }}</small></h1>
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

<div class="row">
	<div class="page-options col-md-12">

		<div class="row">
			<div class="col-md-10">
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
				@if (!$page->is_trashed && !$page->is_home))
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							Add sub page <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							@foreach (Coanda::module('pages')->availablePageTypes($page) as $page_type)
								<li><a href="{{ Coanda::adminUrl('pages/create/' . $page_type->identifier() . '/' . $page->id) }}">{{ $page_type->name() }}</a></li>
							@endforeach
						</ul>
					</div>
				@endif
			</div>
			<div class="col-md-2">
				@if (!$page->is_trashed && !$page->is_draft)				
					<div class="input-group">
						<input type="text" class="form-control select-all" readonly value="{{ url($page->slug) }}">
						<span class="input-group-addon"><a class="new-window" href="{{ url($page->slug) }}"><i class="fa fa-share-square-o"></i></a></span>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">

				@if (!$page->is_home)
					<li class="active"><a href="#subpages" data-toggle="tab">Sub pages ({{ $children->getTotal() }})</a></li>
				@endif

				<li @if ($page->is_home) class="active" @endif><a href="#content" data-toggle="tab">Content</a></li>
				<li><a href="#versions" data-toggle="tab">Versions ({{ $page->versions->count() }})</a></li>
			</ul>
			<div class="tab-content">

				@if (!$page->is_home)
					<div class="tab-pane active" id="subpages">

						@if (Session::has('ordering_updated'))
							<div class="alert alert-success">
								Ordering updated
							</div>
						@endif

						{{ Form::open(['url' => Coanda::adminUrl('pages/view/' . $page->id)]) }}

							@if ($page->parent)
								<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages/view/' . $page->parent->id) }}">Up to {{ $page->parent->present()->name }}</a></p>
							@else
								<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages') }}">Up to Pages</a></p>
							@endif

							@if ($children->count() > 0)
								<table class="table table-striped">
								@foreach ($children as $child)
									<tr class="status-{{ $child->status }}">

										@if (!$page->is_trashed)
											<td class="tight"><input type="checkbox" name="remove_page_list[]" value="{{ $child->id }}" @if (!Coanda::canView('pages', 'remove')) disabled="disabled" @endif></td>
										@endif

										<td>
											@if ($child->is_draft)
												<i class="fa fa-circle-o"></i>
											@else
												<i class="fa fa-circle"></i>
											@endif
											<a href="{{ Coanda::adminUrl('pages/view/' . $child->id) }}">{{ $child->present()->name }}</a>
										</td>
										<td>{{ $child->present()->type }}</td>
										<td>{{ $child->children->count() }} sub page{{ $child->children->count() !== 1 ? 's' : '' }}</td>
										<td>{{ $child->present()->status }}</td>
										@if (!$page->is_trashed)
											<td class="order-column">{{ Form::text('ordering[' . $child->id . ']', $child->order, ['class' => 'form-control input-sm']) }}</td>
											<td class="tight">
												@if ($child->is_draft)
													<a href="{{ Coanda::adminUrl('pages/editversion/' . $child->id . '/1') }}"><i class="fa fa-pencil-square-o"></i></a>
												@else
													<a href="{{ Coanda::adminUrl('pages/edit/' . $child->id) }}"><i class="fa fa-pencil-square-o"></i></a>
												@endif
											</td>
										@endif
									</tr>
								@endforeach
								</table>

								{{ $children->links() }}

								@if (!$page->is_trashed)
									<div class="buttons">
										{{ Form::button('Update ordering', ['name' => 'update_order', 'value' => 'true', 'type' => 'submit', 'class' => 'pull-right btn btn-default']) }}

										@if (Coanda::canView('pages', 'remove'))
											{{ Form::button('Delete selected', ['name' => 'delete_selected', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-danger']) }}
										@else
											<span class="btn btn-danger" disabled="disabled">Delete selected</span>
										@endif
									</div>
								@endif
							@else
								<p>This page doesn't have any sub pages</p>
							@endif

						{{ Form::close() }}
					</div>
				@endif

				<div class="tab-pane @if ($page->is_home) active @endif" id="content">

					<table class="table table-striped">
						@foreach ($page->attributes as $attribute)
						<tr>
							<td class="tight">{{ $attribute->name }}</td>
							<td>
								@include('coanda::admin.pages.pageattributetypes.view.' . $attribute->type, [ 'attribute' => $attribute ])
							</td>
						</tr>
						@endforeach
					</table>

				</div>
				<div class="tab-pane" id="versions">

					<table class="table table-striped">
						@foreach ($page->versions as $version)
							<tr @if ($version->status == 'pending') class="info" @endif>
								<td class="tight">
									@if ($version->status == 'draft' && !$page->is_trashed)
										<a href="{{ Coanda::adminUrl('pages/removeversion/' . $page->id . '/' . $version->version) }}"><i class="fa fa-minus-circle"></i></a>
									@else
										<i class="fa fa-minus-circle fa-disabled"></i>
									@endif
								</td>
								<td>#{{ $version->version }}</td>
								<td>
									Updated: {{ $version->present()->updated_at }}
									@if ($version->status == 'pending')
										<span class="label label-info">Pending</span>
										<i class="fa fa-calendar"></i> Due to be published {{ $version->present()->delayed_publish_date }}
									@else
										@if ($version->status == 'draft')
											<span class="label label-warning">
										@elseif ($version->status == 'published')
											<span class="label label-success">
										@else  
											<span class="label label-default">
										@endif

										{{ $version->present()->status }}</span>
										
									@endif
								</td>
								<td class="tight">
									@if (!$page->is_trashed)
										@if ($version->status == 'draft')
											<a class="new-window" href="{{ url($version->present()->preview_url) }}"><i class="fa fa-share-square-o"></i></a>
											<a href="{{ Coanda::adminUrl('pages/editversion/' . $page->id . '/' . $version->version) }}"><i class="fa fa-pencil-square-o"></i></a>
										@endif
									@endif
								</td>
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
				<li class="active"><a href="#contributors" data-toggle="tab">Contributors</a></li>
				<li><a href="#history" data-toggle="tab">History</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="contributors">
					<table class="table table-striped table-history">
						@foreach ($contributors as $contributor)
							<tr>
								<td class="tight"><img src="{{ $contributor->avatar }}" class="img-circle" width="45"></td>
								<td>{{ $contributor->present()->name }}</td>
							</tr>
						@endforeach
					</table>
				</div>
				<div class="tab-pane" id="history">
					<table class="table table-striped table-history">
						@foreach ($history as $history)
							<tr>
								<td class="tight"><img src="{{ $history->present()->avatar }}" class="img-circle" width="45"></td>
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
