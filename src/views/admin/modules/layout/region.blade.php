@extends('coanda::admin.layout.main')

@section('page_title', 'Layout: ' . $layout->name() . ', ' . $region->name())

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layouts</a></li>
			<li><a href="{{ Coanda::adminUrl('layout/view/' . $layout->identifier()) }}">{{ $layout->name() }}</a></li>
			<li>{{ $region->name() }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">{{ $layout->name() }}, {{ $region->name() }} <small><i class="fa fa-code"></i></small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
				Add new <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				@foreach (Coanda::module('layout')->availableBlockTypes() as $block_type)
					<li><a href="{{ Coanda::adminUrl('layout/create-block/' . $block_type->identifier() . '/' . $layout->identifier() . '/' . $region->identifier()) }}">{{ $block_type->name() }}</a></li>
				@endforeach
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#default-blocks" data-toggle="tab">Default blocks</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="default-blocks">
					@if ($default_blocks)
						<table class="table table-striped">
							@foreach ($default_blocks as $block)
								<tr>
									<td>{{ $block->type }}</td>
									<td><a href="{{ Coanda::adminUrl('layout/block-edit/' . $block->id) }}"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
							@endforeach
						</table>
					@else
						<p>No default blocks have been specified for this region
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@stop