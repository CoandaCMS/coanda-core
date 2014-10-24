@extends('coanda::admin.layout.main')

@section('page_title', 'Pages')

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
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Pages</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ $pages->count() }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
				Add new <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				@foreach (Coanda::module('pages')->availablePageTypes() as $page_type)
					<li><a href="{{ Coanda::adminUrl('pages/create/' . $page_type->identifier()) }}">{{ $page_type->name() }}</a></li>
				@endforeach
			</ul>
		</div>

		@if (!$home_page && Coanda::canView('pages', 'home_page')))
			<div class="btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					Create home page <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					@foreach (Coanda::module('pages')->availableHomePageTypes() as $page_type)
						<li><a href="{{ Coanda::adminUrl('pages/create-home/' . $page_type->identifier()) }}">{{ $page_type->name() }}</a></li>
					@endforeach
				</ul>
			</div>
		@endif
	</div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pages" data-toggle="tab">Pages</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="subpages">

					{{ Form::open(['url' => Coanda::adminUrl('pages')]) }}

						@if (Coanda::canView('pages', 'home_page'))
							<h2>Home page</h2>
							@if ($home_page)
								<table class="table table-striped">
									<tr class="status-{{ $home_page->status }} @if (!$home_page->is_visible || $home_page->is_pending) info @endif @if ($home_page->is_trashed) danger @endif">
										<td>
											@if ($home_page->is_draft)
												<i class="fa fa-circle-o"></i>
											@else
												<i class="fa {{ $home_page->pageType()->icon() }}"></i>
											@endif

											<a href="{{ Coanda::adminUrl('pages/view/' . $home_page->id) }}">{{ $home_page->present()->name }}</a>
										</td>
										<td class="tight">
											@if ($home_page->is_draft)
												<a href="{{ Coanda::adminUrl('pages/editversion/' . $home_page->id . '/1') }}"><i class="fa fa-pencil-square-o"></i></a>
											@else
												<a href="{{ Coanda::adminUrl('pages/edit/' . $home_page->id) }}"><i class="fa fa-pencil-square-o"></i></a>
											@endif
										</td>
									</tr>
								</table>
							@else
								<p>Home page not created</p>
							@endif

							<h2>Top level pages</h2>
						@endif

						@if ($pages->count() > 0)
							@if (Session::has('ordering_updated'))
								<div class="alert alert-success">
									Ordering updated
								</div>
							@endif

							@include('coanda::admin.modules.pages.includes.subpages', [ 'page' => false, 'children' => $pages ])

							{{ $pages->links() }}

							<div class="buttons">
								{{ Form::button('Update ordering', ['name' => 'update_order', 'value' => 'true', 'type' => 'submit', 'class' => 'pull-right btn btn-default']) }}

								@if (Coanda::canView('pages', 'remove'))
									{{ Form::button('Delete selected', ['name' => 'delete_selected', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-danger']) }}
								@else
									<span class="btn btn-danger" disabled="disabled">Delete selected</span>
								@endif
							</div>

						@else
							<p>Your site doesn't have any pages yet!</p>
						@endif

					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
</div>

@stop