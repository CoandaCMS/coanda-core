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
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
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
	<div class="col-md-12">

		<p>Heading?</p>

		<table class="table table-striped">
			@foreach ($pages as $page)
				<tr>
					<td><a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->name !== '' ? $page->name : 'not set' }}</a></td>
					<td>{{ $page->status }}</td>
				</tr>
			@endforeach
		</table>

	</div>
</div>

@stop
