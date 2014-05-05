@extends('coanda::admin.layout.main')

@section('page_title', 'Layout blocks')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li>Layout blocks</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Layout blocks</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ $block_list->getTotal() }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
				Add new block <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				@foreach (Coanda::module('layout')->availableBlockTypes() as $block_type)
					<li><a href="{{ Coanda::adminUrl('layout/block-create/' . $block_type->identifier()) }}">{{ $block_type->name() }}</a></li>
				@endforeach
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#blocks" data-toggle="tab">Layout blocks</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="blocks">

					@if ($block_list->count() > 0)
						<table class="table table-striped">
							@foreach ($block_list as $block)
								<tr class="status-{{ $block->status }}">
									<td>
										<i class="fa fa-th-large"></i>
										<a href="{{ Coanda::adminUrl('layout/block-view/' . $block->id) }}">{{ $block->present()->name }}</a>
									</td>
									<td>{{ $block->blockType()->name() }}</td>
									<td class="tight"><a href="{{ Coanda::adminUrl('layout/block-edit/' . $block->id) }}"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
							@endforeach
						</table>

						{{ $block_list->links() }}
					@else
						<p>No layout blocks have been created.</p>
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
									<td>
										<i class="fa fa-code"></i>
										<a href="{{ Coanda::adminUrl('layout/view/' . $layout->identifier()) }}">{{ $layout->name() }}</a>
									</td>
									<td>{{ $layout->regionCount() }} editable regions</td>
								</tr>
							@endforeach
						</table>
					@else
						<p>No layouts have been specified!</p>
					@endif

				</div>
			</div>
		</div>
	</div>

</div>

@stop