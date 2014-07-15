@extends('coanda::admin.layout.main')

@section('page_title', 'Layout')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layout</a></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Layout</h1>
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
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#blocks" data-toggle="tab">Blocks [{{ $blocks->getTotal() }}]</a></li>
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
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#layouts" data-toggle="tab">Layouts</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="layouts">
					@if (count($layouts) > 0)
						<table class="table table-striped">
							@foreach ($layouts as $layout)
								<tr>
									<td><a href="{{ Coanda::adminUrl('layout/view/' . $layout->identifier()) }}"><i class="fa fa-code"></i> {{ $layout->name() }}</a></td>
									<td>{{ count($layout->regions()) }} regions</td>
								</tr>
							@endforeach
						</table>
					@else
						<p>No layouts have been specified.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@stop