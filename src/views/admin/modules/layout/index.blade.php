@extends('coanda::admin.layout.main')

@section('page_title', 'Layouts')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layouts</a></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Layouts</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ count($layouts) }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li @if (!Input::has('page')) class="active" @endif><a href="#layouts" data-toggle="tab">Layouts</a></li>
				<li @if (Input::has('page')) class="active" @endif><a href="#blocks" data-toggle="tab">All blocks</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane @if (!Input::has('page')) active @endif" id="layouts">

					{{ Form::open(['url' => Coanda::adminUrl('pages')]) }}

						@if (count($layouts) > 0)
							<table class="table table-striped fifty-50">
								@foreach ($layouts as $layout)
									<tr>
										<td><i class="fa fa-code"></i> {{ $layout->name() }}</td>
										<td>
											@if ($layout->regionCount() > 0)
												<table class="table table-striped">
													@foreach ($layout->regions() as $region)
													<tr>
														<td><a href="{{ Coanda::adminUrl('layout/view-region/' . $layout->identifier() . '/' . $region->identifier()) }}">{{ $region->name() }}</a></td>
														<td>{{ $layout->defaultBlockCount($region->identifier()) }} blocks</td>
													</tr>
													@endforeach
												</table>
											@else
												<p>This layout does not have any editable regions.</p>
											@endif
										</td>
									</tr>
								@endforeach
							</table>
						@else
							<p>No layouts have been specified!</p>
						@endif

					{{ Form::close() }}
				</div>

				<div class="tab-pane @if (Input::has('page')) active @endif" id="blocks">

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
</div>

@stop