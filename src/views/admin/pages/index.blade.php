@extends('coanda::admin.layout.main')

@section('page_title', 'Pages')

@section('content')

<div class="row">

	<div class="breadcrumb-nav">
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
				@foreach (Coanda::availablePageTypes() as $page_type)
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
					<table class="table table-striped">
						@foreach ($pages as $page)
							<tr class="status-{{ $page->status }}">
								<td>
									@if ($page->status == 'draft')
										<i class="fa fa-circle-o"></i>
									@else
										<i class="fa fa-circle"></i>
									@endif
									<a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->name !== '' ? $page->name : 'not set' }}</a>
								</td>
								<td>{{ $page->status }}</td>
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
