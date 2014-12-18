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
				<li><a href="{{ Coanda::adminUrl('pages/view/' . $parent->id) }}">{{ $parent->name }}</a></li>
			@endforeach

			<li>{{ $page->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			@if ($page->is_trashed) [Trashed] @endif
			{{ $page->name }}
			<small>
				@if ($page->is_draft)
					<i class="fa fa-circle-o"></i>
				@else
					<i class="fa {{ $page->type_icon }}"></i>
				@endif
				{{ $page->type_name }}
			</small>
		</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Version {{ $page->current_version }}</span>

			@if ($page->is_trashed)
				<span class="label label-danger">{{ $page->status_text }}</span>
			@elseif ($page->is_pending)
				<span class="label label-info">{{ $page->status_text }}</span>
			@else
				<span class="label @if ($page->is_draft) label-warning @else label-success @endif">{{ $page->status_text }}</span>
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
		<i class="fa fa-calendar"></i> {{ $page->visible_dates_text }}
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

@if ($page->is_pending)
	<div class="row">
		<div class="page-visibility col-md-12">
			<span class="label label-info">
				Pending
				<i class="fa fa-calendar"></i>
				{{ $page->currentVersion()->pending_display_text }}
			</span>
		</div>
	</div>
@endif

<div class="row">
	<div class="page-options col-md-12">
		<div class="row">
			<div class="col-md-8">
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
                            @if ($page->can_edit)
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
                                    @if (Coanda::canView('pages', 'remove'))
                                        <a href="{{ Coanda::adminUrl('pages/delete/' . $page->id) }}">Delete</a>
                                    @else
                                        <span class="disabled">Delete</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
				@if (!$page->is_trashed && !$page->is_home && $page->pageType()->allowsSubPages() && $page->can_create)
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							Add sub page <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							@foreach (Coanda::pages()->availablePageTypes($page) as $page_type)
								<li><a href="{{ Coanda::adminUrl('pages/create/' . $page_type->identifier() . '/' . $page->id) }}">{{ $page_type->name() }}</a></li>
							@endforeach
						</ul>
					</div>
				@endif

            </div>
            <div class="col-md-4">
                @if (!$page->is_trashed && !$page->is_draft && !$page->is_pending)
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

			@if (Session::has('info_message'))
				<div class="alert alert-info">
					{{ Session::get('info_message') }}
				</div>
			@endif

			@set('active_tab', Input::get('tab', false))

			@if (!$active_tab)

				@if (!$page->is_home && $page->pageType()->allowsSubPages() && !Input::has('versions_page'))
					@set('active_tab', 'subpages')
				@endif

				@if (($page->is_home || !$page->pageType()->allowsSubPages()) && !Input::has('versions_page'))
					@set('active_tab', 'content')
				@endif

				@if (Input::has('versions_page'))
					@set('active_tab', 'versions')
				@endif

			@endif

			<ul class="nav nav-tabs">
				@if (!$page->is_home && $page->pageType()->allowsSubPages())
					<li @if ($active_tab == 'subpages') class="active" @endif><a href="#subpages" data-toggle="tab">Sub pages ({{ $children->getTotal() }})</a></li>
				@endif

				<li @if ($active_tab == 'content') class="active" @endif><a href="#content" data-toggle="tab">Content</a></li>
				<li @if ($active_tab == 'versions') class="active" @endif><a href="#versions" data-toggle="tab">Versions ({{ $versions->getTotal() }})</a></li>
			</ul>
			<div class="tab-content">

				@if (!$page->is_home && $page->pageType()->allowsSubPages())
					<div class="tab-pane @if ($active_tab == 'subpages') active @endif id="subpages">

						@if (Session::has('ordering_updated'))
							<div class="alert alert-success">
								Ordering updated
							</div>
						@endif

						{{ Form::open(['url' => Coanda::adminUrl('pages/view/' . $page->id)]) }}

							@if ($page->parent)
								<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages/view/' . $page->parent->id) }}">Up to {{ $page->parent->name }}</a></p>
							@else
								<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages') }}">Up to Pages</a></p>
							@endif

							@if ($children->count() > 0)

								@include('coanda::admin.modules.pages.includes.subpages', [ 'page' => $page, 'children' => $children ])

								{{ $children->links() }}

								@if (!$page->is_trashed)
									<div class="buttons">
										<div class="btn-group pull-right">

											@if ($page->sub_page_order == 'manual')
												{{ Form::button('Update orders', ['name' => 'update_order', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
											@endif

											@if ($page->can_edit)
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													Order: {{ $page->sub_page_order_text }} <span class="caret"></span>
												</button>
												<ul class="dropdown-menu" role="menu">
													<li><a href="{{ Coanda::adminUrl('pages/change-page-order/' . $page->id . '/manual') }}"><i class="fa fa-sort-numeric-asc"></i> Manual</a></li>
													<li><a href="{{ Coanda::adminUrl('pages/change-page-order/' . $page->id . '/alpha:asc') }}"><i class="fa fa-sort-alpha-asc"></i> Alphabetical (A-Z)</a></li>
													<li><a href="{{ Coanda::adminUrl('pages/change-page-order/' . $page->id . '/alpha:desc') }}"><i class="fa fa-sort-alpha-desc"></i> Alphabetical (Z-A)</a></li>
													<li><a href="{{ Coanda::adminUrl('pages/change-page-order/' . $page->id . '/created:desc') }}"><i class="fa fa-sort-amount-asc"></i> Created date (Newest-Oldest)</a></li>
													<li><a href="{{ Coanda::adminUrl('pages/change-page-order/' . $page->id . '/created:asc') }}"><i class="fa fa-sort-amount-desc"></i> Created date (Oldest-Newest)</a></li>
												</ul>
											@endif
										</div>

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

				<div class="tab-pane @if ($active_tab == 'content') active @endif " id="content">
					<table class="table table-striped">
						@foreach ($page->currentVersionAttributes() as $attribute)
						<tr>
							<td>{{ $attribute->name }}</td>
							<td>
								@include($attribute->type()->view_template(), [ 'attribute_definition' => $attribute->definition, 'content' => $attribute->type_data ])
							</td>
						</tr>
						@endforeach
					</table>
				</div>

				<div class="tab-pane @if ($active_tab == 'versions') active @endif" id="versions">

					<table class="table table-striped">
						@foreach ($versions as $version)
							<tr @if ($version->status == 'pending') class="info" @endif>
								<td class="tight">
									@if ($version->status == 'draft' && !$page->is_trashed && $page->can_edit)
										<a href="{{ Coanda::adminUrl('pages/removeversion/' . $page->id . '/' . $version->version) }}"><i class="fa fa-minus-circle"></i></a>
									@else
										<i class="fa fa-minus-circle fa-disabled"></i>
									@endif
								</td>
								<td>#{{ $version->version }}</td>
								<td>
									Updated: {{ $version->updated_at }}
									@if ($version->status == 'pending')
										<span class="label label-info">Pending</span>
										{{ $version->pending_display_text }}
									@else
										@if ($version->status == 'draft')
											<span class="label label-warning">
										@elseif ($version->status == 'published')
											<span class="label label-success">
										@else
											<span class="label label-default">
										@endif

										{{ $version->status_text }}</span>

									@endif
								</td>
								<td class="tight">
									@if (!$page->is_trashed)
										<a class="new-window" href="{{ url($version->preview_url) }}"><i class="fa fa-share-square-o"></i></a>

										@if ($version->status == 'draft' && $page->can_edit)
											<a href="{{ Coanda::adminUrl('pages/editversion/' . $page->id . '/' . $version->version) }}"><i class="fa fa-pencil-square-o"></i></a>
										@endif

										@if (($version->status == 'archived' || $version->status == 'published') && $page->can_edit)
											<a href="{{ Coanda::adminUrl('pages/edit/' . $page->id . '/' . $version->version) }}"><i class="fa fa-files-o"></i></a>
										@endif
									@endif
								</td>
							</tr>
						@endforeach
					</table>

                    {{ $versions->links() }}

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
