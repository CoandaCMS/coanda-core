@extends('coanda::admin.layout.main')

@section('page_title', 'Add location')

@section('content')

<div class="row">

	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>Add location</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Add location</h1>
		</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#subpages" data-toggle="tab">Pages</a></li>
			</ul>
			<div class="tab-content">

				<div class="tab-pane active" id="subpages">

					{{ Form::open(['url' => Coanda::adminUrl('pages/add-location/' . $page_id . '/' . $version_number)]) }}

						@if ($location && $location->parent)
							<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages/browse-add-location/' . $page_id . '/' . $version_number . '/' . $location->parent->id) }}">Up to {{ $location->parent->page->present()->name }}</a></p>
						@else
							<p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages/browse-add-location/' . $page_id . '/' . $version_number) }}">Up to Pages</a></p>
						@endif

						@if ($pages->count() > 0)
							<table class="table table-striped">
							@foreach ($pages as $pagelocation)
								<tr class="status-{{ $pagelocation->page->status }}">

									@if (!$pagelocation->is_trashed)
										<td class="tight"><input type="checkbox" name="add_locations[]" value="{{ $pagelocation->id }}"></td>
									@endif

									<td>
										@if ($pagelocation->page->is_draft)
											<i class="fa fa-circle-o"></i>
										@else
											<i class="fa {{ $pagelocation->page->pageType()->icon() }}"></i>
										@endif
										<a href="{{ Coanda::adminUrl('pages/browse-add-location/' . $page_id . '/' . $version_number . '/' . $pagelocation->id) }}">{{ $pagelocation->page->present()->name }}</a>
									</td>
									<td>{{ $pagelocation->page->present()->type }}</td>
									<td>{{ $pagelocation->page->present()->status }}</td>
								</tr>
							@endforeach
							</table>

							{{ $pages->links() }}

							<div class="buttons">
								{{ Form::button('Add selected locations', ['name' => 'add_selected_locations', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
								<a href="{{ Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number) }}" class="btn btn-default">Cancel</a>
							</div>

						@else
							<p>This page doesn't have any sub pages</p>
						@endif

					{{ Form::close() }}
				</div>

			</div>
		</div>
	</div>
</div>

@stop
