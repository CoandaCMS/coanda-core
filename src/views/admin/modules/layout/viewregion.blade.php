@extends('coanda::admin.layout.main')

@section('page_title', 'Layout: ' . $layout->name() . '/' . $region->name())

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layouts</a></li>
			<li>{{ $layout->name() }}/{{ $region->name() }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">{{ $layout->name() }}/{{ $region->name() }} <small><i class="fa fa-code"></i></small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
				Add default block <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				@foreach (Coanda::module('layout')->availableBlockTypes() as $block_type)
					<li><a href="{{ Coanda::adminUrl('layout/block-create/' . $block_type->identifier() . '/' . $layout->identifier() . '/' . $region->identifier()) }}">{{ $block_type->name() }}</a></li>
				@endforeach
			</ul>
		</div>
	</div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('layout/view-region/' . $layout->identifier() . '/' . $region->identifier())]) }}
<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#blocks" data-toggle="tab">Default Blocks</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="blocks">

					@if (Session::has('ordering_updated'))
						<div class="alert alert-success">
							Ordering updated
						</div>
					@endif

					@if ($region_blocks->count() > 0)
						<table class="table table-striped">
							@foreach ($region_blocks as $region_block)
								<tr class="status-{{ $region_block->block->status }}">
									<td>
										<i class="fa fa-th-large"></i>
										<a href="{{ Coanda::adminUrl('layout/block-view/' . $region_block->block->id) }}">{{ $region_block->block->present()->name }}</a>
									</td>
									<td>{{ $region_block->block->blockType()->name() }}</td>
									<td class="order-column">{{ Form::text('ordering[' . $region_block->id . ']', $region_block->order, ['class' => 'form-control input-sm']) }}</td>
									<td class="tight"><a href="{{ Coanda::adminUrl('layout/block-edit/' . $region_block->block->id) }}"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
							@endforeach
						</table>

						<div class="buttons">
							{{ Form::button('Update ordering', ['name' => 'update_order', 'value' => 'true', 'type' => 'submit', 'class' => 'pull-right btn btn-default']) }}
						</div>
					@else
						<p>This layout region does not have any default blocks specified.</p>
					@endif

				</div>
			</div>
		</div>
	</div>
</div>
{{ Form::close() }}

@stop