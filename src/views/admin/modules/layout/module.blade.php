@extends('coanda::admin.layout.main')

@section('page_title', 'Layout: ' . $layout->name() . ' / ' . $region_name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layout</a></li>
			<li><a href="{{ Coanda::adminUrl('layout/view/' . $layout->identifier()) }}">{{ $layout->name() }}</a></li>
			<li><a href="{{ Coanda::adminUrl('layout/region/' . $layout->identifier() . '/' . $region_identifier) }}">{{ $region_name }}</a></li>
			<li>{{ $module_identifier }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			{{ $layout->name() }} / {{ $region_name }} / {{ $module_identifier }}
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
				<li class="active"><a href="#regions" data-toggle="tab">Blocks</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="regions">

					@if (Session::has('ordering_updated'))
						<div class="alert alert-success">
							Ordering updated
						</div>
					@endif

					{{ Form::open(['url' => Coanda::adminUrl('layout/module/' . $layout->identifier() . '/' . $region_identifier . '/' . $module_identifier)]) }}

						@if ($assignments->count() > 0)
							<table class="table table-striped">
								@foreach ($assignments as $assignment)
									<tr>
										<td><a href="{{ Coanda::adminUrl('layout/block/' . $assignment->block_id) }}"><i class="fa fa-cubes"></i> {{ $assignment->block->name }}</a></td>
										<td>{{ $assignment->block->block_type->name() }}</td>
										<td class="tight">
											<td class="order-column">{{ Form::text('ordering[' . $assignment->id . ']', $assignment->order, ['class' => 'form-control input-sm']) }}</td>
										</td>
									</tr>
								@endforeach
							</table>
						@else
							<p>This module doesn't have any blocks assigned.</p>
						@endif

						<div class="pull-right">
							{{ Form::button('Update orders', ['name' => 'update_order', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
						</div>

					{{ Form::close() }}

				</div>
			</div>
		</div>
	</div>
</div>

@stop