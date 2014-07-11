@extends('coanda::admin.layout.main')

@section('page_title', 'Layout blocks')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layout blocks</a></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Layout blocks</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ $blocks->getTotal() }}</span>
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
				@foreach (Coanda::layout()->blockTypes() as $block_type)
					<li><a href="{{ Coanda::adminUrl('layout/add-block/' . $block_type->identifier()) }}">{{ $block_type->name() }}</a></li>
				@endforeach
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#blocks" data-toggle="tab">Blocks</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="blocks">
					@if ($blocks->count() > 0)
						<table class="table table-striped">
							@foreach ($blocks as $block)
								<tr>
									<td><a href="{{ Coanda::adminUrl('layout/block/' . $block->id) }}"><i class="fa fa-cubes"></i> {{ $block->name }}</a></td>
									<td>{{ $block->block_type->name() }}</td>
									<td class="tight"><a href="{{ Coanda::adminUrl('layout/edit-block/' . $block->id) }}"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
							@endforeach
						</table>
					@else
						<p>No blocks have been added!</p>
					@endif

				</div>
			</div>
		</div>
	</div>
</div>

@stop