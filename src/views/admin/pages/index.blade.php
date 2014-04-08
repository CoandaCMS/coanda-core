@extends('coanda::admin.layout.main')

@section('page_title', 'Pages')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<div class="pull-right">
			<a href="{{ Coanda::adminUrl('pages/trash') }}" class="trash-icon"><i class="fa fa-trash-o"></i> Trash</a>
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
					<li><a href="{{ Coanda::adminUrl('pages/create/' . $page_type->identifier) }}">{{ $page_type->name }}</a></li>
				@endforeach
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pages" data-toggle="tab">Top level pages</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="subpages">

					@if ($pages->count() > 0)
						@if (Session::has('ordering_updated'))
							<div class="alert alert-success">
								Ordering updated
							</div>
						@endif

						{{ Form::open(['url' => Coanda::adminUrl('pages')]) }}
							<table class="table table-striped">
								@foreach ($pages as $page)
									<tr class="status-{{ $page->status }}">
										<td class="tight"><input type="checkbox" name="remove_page_list[]" value="{{ $page->id }}"></td>
										<td>
											@if ($page->is_draft)
												<i class="fa fa-circle-o"></i>
											@else
												<i class="fa fa-circle"></i>
											@endif
											<a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->present()->name }}</a>
										</td>
										<td>{{ $page->present()->type }}</td>
										<td>{{ $page->children->count() }} sub page{{ $page->children->count() !== 1 ? 's' : '' }}</td>
										<td>{{ $page->present()->status }}</td>
										<td class="order-column">{{ Form::text('ordering[' . $page->id . ']', $page->order, ['class' => 'form-control input-sm']) }}</td>
										<td class="tight">
											@if ($page->is_draft)
												<a href="{{ Coanda::adminUrl('pages/editversion/' . $page->id . '/1') }}"><i class="fa fa-pencil-square-o"></i></a>
											@else
												<a href="{{ Coanda::adminUrl('pages/edit/' . $page->id) }}"><i class="fa fa-pencil-square-o"></i></a>
											@endif
										</td>
									</tr>
								@endforeach
							</table>

							{{ $pages->links() }}

							<div class="buttons">
								{{ Form::button('Update ordering', ['name' => 'update_order', 'value' => 'true', 'type' => 'submit', 'class' => 'pull-right btn btn-default']) }}
								{{ Form::button('Delete selected', ['name' => 'delete_selected', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-danger']) }}
							</div>
						{{ Form::close() }}
					@else
						<p>Your site doesn't have any pages yet!</p>
					@endif
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#search" data-toggle="tab">Search</a></li>
				<li><a href="#other" data-toggle="tab">Other</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="search">
					<input type="text" class="form-control" placeholder="Search pages">
				</div>

				<div class="tab-pane" id="other">
					Something else
				</div>
			</div>
		</div>
	</div>
</div>

@stop